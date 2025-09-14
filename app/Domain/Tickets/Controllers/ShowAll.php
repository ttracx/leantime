<?php

namespace Safe4Work\Domain\Tickets\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Sprints\Services\Sprints as SprintService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Symfony\Component\HttpFoundation\Response;

class ShowAll extends Controller
{
    private ProjectService $projectService;

    private TicketService $ticketService;

    private SprintService $sprintService;

    private TimesheetService $timesheetService;

    public function init(
        ProjectService $projectService,
        TicketService $ticketService,
        SprintService $sprintService,
        TimesheetService $timesheetService
    ): void {

        $this->projectService = $projectService;
        $this->ticketService = $ticketService;
        $this->sprintService = $sprintService;
        $this->timesheetService = $timesheetService;

        session(['lastPage' => CURRENT_URL]);
        session(['lastTicketView' => 'table']);
        session(['lastFilterdTicketTableView' => CURRENT_URL]);

        if (! session()->exists('currentProjectName')) {
            Frontcontroller::redirect(BASE_URL.'/');
        }
    }

    /**
     * @throws \Exception
     */
    public function get($params): Response
    {
        $template_assignments = $this->ticketService->getTicketTemplateAssignments($params);
        array_map([$this->tpl, 'assign'], array_keys($template_assignments), array_values($template_assignments));

        return $this->tpl->display('tickets.showAll');
    }
}
