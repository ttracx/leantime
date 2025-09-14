<?php

namespace Safe4Work\Domain\Projects\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\Controller\Frontcontroller as FrontcontrollerCore;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Projects\Repositories\Projects as ProjectRepository;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;

class DelProject extends Controller
{
    private ProjectRepository $projectRepo;

    private ProjectService $projectService;

    /**
     * init - initialize private variables
     */
    public function init(ProjectRepository $projectRepo, ProjectService $projectService)
    {
        $this->projectRepo = $projectRepo;
        $this->projectService = $projectService;
    }

    /**
     * run - display template and edit data
     */
    public function run()
    {

        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager], true);

        // Only admins
        if (Auth::userIsAtLeast(Roles::$manager)) {
            if (isset($_GET['id']) === true) {
                $id = (int) ($_GET['id']);

                if ($this->projectRepo->hasTickets($id)) {
                    $this->tpl->setNotification($this->language->__('notification.project_has_tasks'), 'info');
                }

                if (isset($_POST['del']) === true) {
                    $this->projectRepo->deleteProject($id);
                    $this->projectRepo->deleteAllUserRelations($id);

                    $this->projectService->resetCurrentProject();
                    $this->projectService->setCurrentProject();

                    $this->tpl->setNotification($this->language->__('notification.project_deleted'), 'success');

                    return Frontcontroller::redirect(BASE_URL.'/projects/showAll');
                }

                // Assign vars
                $project = $this->projectRepo->getProject($id);
                if ($project === false) {
                    return FrontcontrollerCore::redirect(BASE_URL.'/errors/error404');
                }

                $this->tpl->assign('project', $project);

                return $this->tpl->display('projects.delProject');
            } else {
                return $this->tpl->display('errors.error403', responseCode: 403);
            }
        } else {
            return $this->tpl->display('errors.error403', responseCode: 403);
        }
    }
}
