<?php

namespace Safe4Work\Domain\Users\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Ldap\Services\Ldap as LdapService;
use Safe4Work\Domain\Users\Repositories\Users as UserRepository;
use Symfony\Component\HttpFoundation\Response;

class ShowAll extends Controller
{
    private UserRepository $userRepo;

    private LdapService $ldapService;

    public function init(UserRepository $userRepo, LdapService $ldapService): void
    {
        $this->userRepo = $userRepo;
        $this->ldapService = $ldapService;
    }

    /**
     * @throws \Exception
     */
    public function get(): Response
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        // Only Admins
        if (Auth::userIsAtLeast(Roles::$admin)) {
            if (Auth::userIsAtLeast(Roles::$admin)) {
                $this->tpl->assign('allUsers', $this->userRepo->getAll());
            } else {
                $this->tpl->assign('allUsers', $this->userRepo->getAllClientUsers(Auth::getUserClientId()));
            }

            $this->tpl->assign('admin', true);
            $this->tpl->assign('roles', Roles::getRoles());

            return $this->tpl->display('users.showAll');
        } else {
            return $this->tpl->display('errors.error403');
        }
    }

    public function post($params): Response
    {
        return $this->tpl->displayJson(['status' => 'Not Implemented']);
    }
}
