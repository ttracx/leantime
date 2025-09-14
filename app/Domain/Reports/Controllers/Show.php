<?php

namespace Safe4Work\Domain\Reports\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Dashboard\Repositories\Dashboard as DashboardRepository;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Sprints\Services\Sprints as SprintService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users as UserService;
use Symfony\Component\HttpFoundation\Response;

class Show extends Controller
{
    private DashboardRepository $dashboardRepo;

    private ProjectService $projectService;

    private SprintService $sprintService;

    private TicketService $ticketService;

    private UserService $userService;

    private TimesheetService $timesheetService;

    private ReportService $reportService;

    /**
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     */
    public function init(
        DashboardRepository $dashboardRepo,
        ProjectService $projectService,
        SprintService $sprintService,
        TicketService $ticketService,
        UserService $userService,
        TimesheetService $timesheetService,
        ReportService $reportService
    ): void {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $this->dashboardRepo = $dashboardRepo;
        $this->projectService = $projectService;
        $this->sprintService = $sprintService;
        $this->ticketService = $ticketService;
        $this->userService = $userService;
        $this->timesheetService = $timesheetService;

        session(['lastPage' => BASE_URL.'/reports/show']);

        $this->reportService = $reportService;
        $this->reportService->dailyIngestion();
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(): Response
    {

        // Project Progress
        $progress = $this->projectService->getProjectProgress(session('currentProject'));

        $this->tpl->assign('projectProgress', $progress);
        $this->tpl->assign(
            'currentProjectName',
            $this->projectService->getProjectName(session('currentProject'))
        );

        // Sprint Burndown

        $allSprints = $this->sprintService->getAllSprints(session('currentProject'));

        $sprintChart = false;

        if ($allSprints !== false && count($allSprints) > 0) {
            if (isset($_GET['sprint'])) {
                $sprintObject = $this->sprintService->getSprint((int) $_GET['sprint']);
                if ($sprintObject) {
                    $sprintChart = $this->sprintService->getSprintBurndown($sprintObject);
                }
                $this->tpl->assign('currentSprint', (int) $_GET['sprint']);
            } else {
                $currentSprint = $this->sprintService->getCurrentSprintId((int) session('currentProject'));

                if ($currentSprint !== false && $currentSprint !== 'all') {
                    $sprintObject = $this->sprintService->getSprint((int) $currentSprint);
                    if ($sprintObject) {
                        $sprintChart = $this->sprintService->getSprintBurndown($sprintObject);
                    }
                    $this->tpl->assign('currentSprint', $sprintObject->id);
                } else {
                    $sprintChart = $this->sprintService->getSprintBurndown($allSprints[0]);
                    $this->tpl->assign('currentSprint', $allSprints[0]->id);
                }
            }
        }

        $this->tpl->assign('sprintBurndown', $sprintChart);
        $this->tpl->assign('backlogBurndown', $this->sprintService->getCummulativeReport(session('currentProject')));

        $this->tpl->assign('allSprints', $this->sprintService->getAllSprints(session('currentProject')));

        $fullReport = $this->reportService->getFullReport(session('currentProject'));

        $this->tpl->assign('fullReport', $fullReport);
        $this->tpl->assign('fullReportLatest', $this->reportService->getRealtimeReport(session('currentProject'), ''));

        $this->tpl->assign('states', $this->ticketService->getStatusLabels());

        // Milestones

        $allProjectMilestones = $this->ticketService->getAllMilestones(['sprint' => '', 'type' => 'milestone', 'currentProject' => session('currentProject')]);
        $this->tpl->assign('milestones', $allProjectMilestones);

        return $this->tpl->display('reports.show');
    }

    public function post($params): Response
    {
        return Frontcontroller::redirect(BASE_URL.'/dashboard/show');
    }
}
