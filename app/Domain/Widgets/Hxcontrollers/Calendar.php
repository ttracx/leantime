<?php

namespace Safe4Work\Domain\Widgets\Hxcontrollers;

use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users as UserService;

class Calendar extends HtmxController
{
    protected static string $view = 'widgets::partials.calendar';

    private ProjectService $projectsService;

    private TicketService $ticketsService;

    private UserService $usersService;

    private TimesheetService $timesheetsService;

    private ReportService $reportsService;

    private SettingRepository $settingRepo;

    private CalendarRepository $calendarRepo;

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
        CalendarRepository $calendarRepo
    ) {
        $this->projectsService = $projectsService;
        $this->ticketsService = $ticketsService;
        $this->usersService = $usersService;
        $this->timesheetsService = $timesheetsService;
        $this->reportsService = $reportsService;
        $this->settingRepo = $settingRepo;
        $this->calendarRepo = $calendarRepo;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }

    public function get()
    {

        $this->tpl->assign('externalCalendars', $this->calendarRepo->getMyExternalCalendars(session('userdata.id')));
        $this->tpl->assign('calendar', $this->calendarRepo->getCalendar(session('userdata.id')));
    }
}
