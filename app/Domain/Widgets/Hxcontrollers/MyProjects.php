<?php

namespace Safe4Work\Domain\Widgets\Hxcontrollers;

use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users as UserService;

class MyProjects extends HtmxController
{
    protected static string $view = 'widgets::partials.myProjects';

    private ProjectService $projectsService;

    private TicketService $ticketsService;

    private UserService $usersService;

    private TimesheetService $timesheetsService;

    private ReportService $reportsService;

    private SettingRepository $settingRepo;

    private CalendarRepository $calendarRepo;

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
        Menu $menuService
    ) {
        $this->projectsService = $projectsService;
        $this->ticketsService = $ticketsService;
        $this->usersService = $usersService;
        $this->timesheetsService = $timesheetsService;
        $this->reportsService = $reportsService;
        $this->settingRepo = $settingRepo;
        $this->calendarRepo = $calendarRepo;
        $this->menuService = $menuService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }

    public function get()
    {

        $allprojects = $this->projectsService->getProjectsAssignedToUser(session('userdata.id'), 'open');
        $clients = [];

        $projectResults = [];
        $i = 0;

        $clientId = '';

        $this->tpl->assign('background', $_GET['noBackground'] ?? '');
        $this->tpl->assign('type', $_GET['type'] ?? 'simple');

        if (is_array($allprojects)) {
            foreach ($allprojects as $project) {
                if (! array_key_exists($project['clientId'], $clients)) {
                    $clients[$project['clientId']] = $project['clientName'];
                }

                if ($clientId == '' || $project['clientId'] == $clientId) {
                    $projectResults[$i] = $project;
                    $projectResults[$i]['progress'] = $this->projectsService->getProjectProgress($project['id']);

                    $fullReport = $this->reportsService->getRealtimeReport($project['id'], '');

                    $projectResults[$i]['report'] = $fullReport;

                    $i++;
                }
            }
        }

        $projectTypeAvatars = $this->menuService->getProjectTypeAvatars();

        $this->tpl->assign('projectTypeAvatars', $projectTypeAvatars);

        $this->tpl->assign('allProjects', $projectResults);
    }
}
