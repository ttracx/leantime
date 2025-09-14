<?php

namespace Safe4Work\Domain\Projects\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Clients\Repositories\Clients as ClientRepository;
use Safe4Work\Domain\Comments\Services\Comments as CommentService;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;

class ShowMy extends Controller
{
    private ProjectService $projectService;

    private TicketService $ticketService;

    private ReportService $reportService;

    private CommentService $commentService;

    private ClientRepository $clientRepo;

    private Menu $menuService;

    public function init(
        ProjectService $projectService,
        TicketService $ticketService,
        ReportService $reportService,
        CommentService $commentService,
        ClientRepository $clientRepo,
        Menu $menuService
    ): void {
        $this->projectService = $projectService;
        $this->ticketService = $ticketService;
        $this->reportService = $reportService;
        $this->commentService = $commentService;
        $this->clientRepo = $clientRepo;
        $this->menuService = $menuService;
    }

    /**
     * run - display template and edit data
     */
    public function get()
    {

        $clientId = '';
        $currentClientName = '';

        if (isset($_GET['client']) === true && $_GET['client'] != '') {
            $clientId = (int) $_GET['client'];
            $currentClient = $this->clientRepo->getClient($clientId);
            if (is_array($currentClient) && count($currentClient) > 0) {
                $currentClientName = $currentClient['name'];
            }
        }

        $allprojects = $this->projectService->getProjectsAssignedToUser(session('userdata.id'), 'open');
        $clients = [];

        $projectResults = [];
        $i = 0;

        if (is_array($allprojects)) {
            foreach ($allprojects as $project) {

                if (! array_key_exists($project['clientId'], $clients)) {
                    $clients[$project['clientId']] = ['name' => $project['clientName'], 'id' => $project['clientId']];
                }

                if ($clientId == '' || $project['clientId'] == $clientId) {
                    $projectResults[$i] = $project;
                    $i++;
                }
            }
        }

        $projectTypeAvatars = $this->menuService->getProjectTypeAvatars();

        $this->tpl->assign('projectTypeAvatars', $projectTypeAvatars);
        $this->tpl->assign('currentClientName', $currentClientName);
        $this->tpl->assign('currentClient', $clientId);
        $this->tpl->assign('clients', $clients);
        $this->tpl->assign('allProjects', $projectResults);

        return $this->tpl->display('projects.projectHub');
    }
}
