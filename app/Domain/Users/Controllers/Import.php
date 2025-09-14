<?php

/* Not production ready yet. Prepping for future version */

namespace Safe4Work\Domain\Users\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\UI\Template as TemplateCore;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Ldap\Services\Ldap as LdapService;
use Safe4Work\Domain\Users\Repositories\Users as UserRepository;
use Symfony\Component\HttpFoundation\Response;

class Import extends Controller
{
    private UserRepository $userRepo;

    private LdapService $ldapService;

    public function init(UserRepository $userRepo, LdapService $ldapService): void
    {
        $this->userRepo = $userRepo;
        $this->ldapService = $ldapService;

        if (! session()->exists('tmp')) {
            session(['tmp' => []]);
        }
    }

    /**
     * @throws \Exception
     */
    public function get(): Response
    {
        // Only Admins
        if (! Auth::userIsAtLeast(Roles::$admin)) {
            return $this->tpl->display('errors.error403');
        }

        $this->tpl->assign('allUsers', $this->userRepo->getAll());
        $this->tpl->assign('admin', true);
        $this->tpl->assign('roles', Roles::getRoles());

        if (session()->exist('tmp.ldapUsers') && count(session('tmp.ldapUsers')) > 0) {
            $this->tpl->assign('allLdapUsers', session('tmp.ldapUsers'));
            $this->tpl->assign('confirmUsers', true);
        }

        return $this->tpl->displayPartial('users.importLdapDialog');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {
        $this->tpl = app()->make(TemplateCore::class);
        $this->ldapService = app()->make(LdapService::class);

        // Password Submit to connect to ldap and retrieve users. Sets tmp session var
        if (isset($params['pwSubmit'])) {
            $username = $this->ldapService->extractLdapFromUsername(session('userdata.mail'));

            $this->ldapService->connect();

            if ($this->ldapService->bind($username, $params['password'])) {
                session(['tmp.ldapUsers' => $this->ldapService->getAllMembers()]);
                $this->tpl->assign('allLdapUsers', session('tmp.ldapUsers'));
                $this->tpl->assign('confirmUsers', true);
            } else {
                $this->tpl->setNotification($this->language->__('notifications.username_or_password_incorrect'), 'error');
            }
        }

        // Import/Update User Post
        if (isset($params['importSubmit'])) {
            if (is_array($params['users'])) {
                $users = [];
                foreach (session('tmp.ldapUsers') as $user) {
                    if (array_search($user['username'], $params['users'])) {
                        $users[] = $user;
                    }
                }

                $this->ldapService->upsertUsers($users);
            }
        }

        return $this->tpl->displayPartial('users.importLdapDialog');
    }
}
