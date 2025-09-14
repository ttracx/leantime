<?php

namespace Safe4Work\Domain\Projects\Hxcontrollers;

use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Safe4Work\Domain\Clients\Repositories\Clients;
use Safe4Work\Domain\Comments\Services\Comments;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users as UserService;

class ProjectHubProjects extends HtmxController
{
    protected static string $view = 'projects::partials.projectHubProjects';

    private ProjectService $projectsService;

    private TicketService $ticketsService;

    private UserService $usersService;

    private TimesheetService $timesheetsService;

    private ReportService $reportsService;

    private SettingRepository $settingRepo;

    private CalendarRepository $calendarRepo;

    private Clients $clientRepo;

    private Comments $commentsService;

    private Menu $menuService;

    /**
     * Controller constructor
     *
     * @param  \Leantime\Domain\Projects\Services\Projects  $projectService  The projects domain service.
     * @return void
     */
    public function init(
        ProjectService $projectsService,
        TicketService $ticketsService,
        UserService $usersService,
        TimesheetService $timesheetsService,
        ReportService $reportsService,
        SettingRepository $settingRepo,
        CalendarRepository $calendarRepo,
        Clients $clientRepo,
        Comments $commentsService,
        Menu $menuService
    ) {
        $this->projectsService = $projectsService;
        $this->ticketsService = $ticketsService;
        $this->usersService = $usersService;
        $this->timesheetsService = $timesheetsService;
        $this->reportsService = $reportsService;
        $this->settingRepo = $settingRepo;
        $this->calendarRepo = $calendarRepo;
        $this->clientRepo = $clientRepo;
        $this->commentsService = $commentsService;
        $this->menuService = $menuService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }

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

        $allprojects = $this->projectsService->getProjectsAssignedToUser(session('userdata.id'), 'open');
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

        $currentUrlPath = BASE_URL.'/'.str_replace('.', '/', Frontcontroller::getCurrentRoute());

        $this->tpl->assign('projectTypeAvatars', $projectTypeAvatars);
        $this->tpl->assign('currentUrlPath', $currentUrlPath);
        $this->tpl->assign('currentClientName', $currentClientName);
        $this->tpl->assign('currentClient', $clientId);
        $this->tpl->assign('clients', $clients);
        $this->tpl->assign('allProjects', $projectResults);
    }
}
