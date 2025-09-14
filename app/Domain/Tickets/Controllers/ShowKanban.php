<?php

namespace Safe4Work\Domain\Tickets\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Sprints\Services\Sprints as SprintService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Symfony\Component\HttpFoundation\Response;

class ShowKanban extends Controller
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
        session(['lastTicketView' => 'kanban']);
        session(['lastFilterdTicketKanbanView' => CURRENT_URL]);
    }

    /**
     * @throws \Exception
     */
    public function get(array $params): Response
    {
        $template_assignments = $this->ticketService->getTicketTemplateAssignments($params);
        array_map([$this->tpl, 'assign'], array_keys($template_assignments), array_values($template_assignments));

        $this->tpl->assign('allKanbanColumns', $this->ticketService->getKanbanColumns());

        return $this->tpl->display('tickets.showKanban');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post(array $params): Response
    {
        // QuickAdd
        if (isset($_POST['quickadd'])) {
            $result = $this->ticketService->quickAddTicket($params);

            if (is_array($result)) {
                $this->tpl->setNotification($result['message'], $result['status']);
            }
        }

        return Frontcontroller::redirect(CURRENT_URL);
    }
}
