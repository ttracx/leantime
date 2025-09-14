<?php

namespace Safe4Work\Domain\Api\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Api\Services\Api as ApiService;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Projects\Repositories\Projects as ProjectRepository;
use Safe4Work\Domain\Users\Repositories\Users as UserRepository;
use Safe4Work\Domain\Users\Services\Users as UserService;
use Symfony\Component\HttpFoundation\Response;

class NewApiKey extends Controller
{
    private UserRepository $userRepo;

    private ProjectRepository $projectsRepo;

    private UserService $userService;

    private ApiService $APIService;

    /**
     * init - initialize private variables
     *
     * @throws BindingResolutionException
     */
    public function init(
        UserRepository $userRepo,
        ProjectRepository $projectsRepo,
        UserService $userService,
        ApiService $APIService
    ): void {

        self::dispatch_event('api_key_init', $this);

        $this->userRepo = $userRepo;
        $this->projectsRepo = $projectsRepo;
        $this->userService = $userService;
        $this->APIService = $APIService;
    }

    /**
     * run - display template and edit data
     *
     *
     *
     * @throws \Exception
     */
    public function run(): Response
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        $values = [
            'firstname' => '',
            'lastname' => '',
            'user' => '',
            'role' => '',
            'password' => '',
            'status' => 'a',
            'source' => 'api',
        ];

        // only Admins
        if (Auth::userIsAtLeast(Roles::$admin)) {
            $projectRelation = [];

            if (isset($_POST['save'])) {
                $values = [
                    'firstname' => ($_POST['firstname']),
                    'user' => '',
                    'role' => ($_POST['role']),
                    'password' => '',
                    'pwReset' => '',
                    'status' => '',
                    'source' => 'api',
                ];

                if (isset($_POST['projects']) && is_array($_POST['projects'])) {
                    foreach ($_POST['projects'] as $project) {
                        $projectRelation[] = $project;
                    }
                }

                $apiKeyValues = $this->APIService->createAPIKey($values);

                // Update Project Relationships
                if (isset($_POST['projects']) && count($_POST['projects']) > 0) {
                    if ($_POST['projects'][0] !== '0') {
                        $this->projectsRepo->editUserProjectRelations($apiKeyValues['id'], $_POST['projects']);
                    } else {
                        $this->projectsRepo->deleteAllProjectRelations($apiKeyValues['id']);
                    }
                }

                $this->tpl->setNotification('notifications.key_created', 'success', 'apikey_created');

                $this->tpl->assign('apiKeyValues', $apiKeyValues);
            }

            $this->tpl->assign('values', $values);

            $this->tpl->assign('allProjects', $this->projectsRepo->getAll());
            $this->tpl->assign('roles', Roles::getRoles());

            $this->tpl->assign('relations', $projectRelation);

            return $this->tpl->displayPartial('api.newAPIKey');
        } else {
            return $this->tpl->displayPartial('errors.error403');
        }
    }
}
