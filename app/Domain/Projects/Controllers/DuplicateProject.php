<?php

namespace Safe4Work\Domain\Projects\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Clients\Repositories\Clients as ClientRepository;
use Safe4Work\Domain\Projects\Repositories\Projects as ProjectRepository;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Symfony\Component\HttpFoundation\Response;

class DuplicateProject extends Controller
{
    private ProjectService $projectService;

    private ClientRepository $clientRepo;

    private ProjectRepository $projectRepo;

    public function init(
        ProjectRepository $projectRepo,
        ClientRepository $clientRepo,
        ProjectService $projectService
    ): void {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager], true);

        $this->projectRepo = $projectRepo;
        $this->clientRepo = $clientRepo;
        $this->projectService = $projectService;
    }

    /**
     * @throws \Exception
     */
    public function get(): Response
    {
        if (
            ! Auth::userIsAtLeast(Roles::$manager)
            || ! isset($_GET['id'])
        ) {
            return $this->tpl->displayPartial('errors.error403', responseCode: 403);
        }

        $id = (int) ($_GET['id']);
        $project = $this->projectService->getProject($id);

        $this->tpl->assign('allClients', $this->clientRepo->getAll());
        $this->tpl->assign('project', $project);

        return $this->tpl->displayPartial('projects.duplicateProject');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {

        // Only admins
        if (Auth::userIsAtLeast(Roles::$manager)) {
            $id = (int) ($_GET['id']);
            $projectName = $params['projectName'];
            $startDate = $_POST['startDate'] ?? '';
            $clientId = (int) $params['clientId'];
            $assignSameUsers = false;

            if (isset($params['assignSameUsers'])) {
                $assignSameUsers = true;
            }

            $result = $this->projectService->duplicateProject($id, $clientId, $projectName, $startDate, $assignSameUsers);

            $this->tpl->setNotification(sprintf($this->language->__('notifications.project_copied_successfully'), BASE_URL.'/projects/changeCurrentProject/'.$result), 'success');

            return Frontcontroller::redirect(BASE_URL.'/projects/duplicateProject/'.$id);
        } else {
            return $this->tpl->displayPartial('errors.error403');
        }
    }
}
