<?php

namespace Safe4Work\Domain\Projects\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Menu\Repositories\Menu as MenuRepository;
use Safe4Work\Domain\Projects\Repositories\Projects as ProjectRepository;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;

class ShowAll extends Controller
{
    private ProjectRepository $projectRepo;

    private MenuRepository $menuRepo;

    private ProjectService $projectService;

    /**
     * init - initialize private variables
     */
    public function init(
        ProjectRepository $projectRepo,
        MenuRepository $menuRepo,
        ProjectService $projectService
    ) {
        $this->projectRepo = $projectRepo;
        $this->projectService = $projectService;
        $this->menuRepo = $menuRepo;
    }

    /**
     * run - display template and edit data
     */
    public function run()
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager], true);

        if (Auth::userIsAtLeast(Roles::$manager)) {
            if (! session()->exists('showClosedProjects')) {
                session(['showClosedProjects' => false]);
            }

            if (isset($_POST['hideClosedProjects'])) {
                session(['showClosedProjects' => false]);
            }

            if (isset($_POST['showClosedProjects'])) {
                session(['showClosedProjects' => true]);
            }

            $this->tpl->assign('role', session('userdata.role'));

            if (Auth::userIsAtLeast(Roles::$admin)) {
                $this->tpl->assign('allProjects', $this->projectRepo->getAll(session('showClosedProjects')));
            } else {
                $this->tpl->assign('allProjects', $this->projectService->getClientManagerProjects(session('userdata.id'), session('userdata.clientId')));
            }
            $this->tpl->assign('menuTypes', $this->menuRepo->getMenuTypes());

            $this->tpl->assign('showClosedProjects', session('showClosedProjects'));

            return $this->tpl->display('projects.showAll');
        } else {
            return $this->tpl->display('errors.error403', responseCode: 403);
        }
    }
}
