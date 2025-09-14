<?php

namespace Safe4Work\Domain\Tickets\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller as FrontcontrollerCore;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Symfony\Component\HttpFoundation\Response;

class MoveTicket extends Controller
{
    private TicketService $ticketService;

    private ProjectService $projectService;

    public function init(
        TicketService $ticketService,
        ProjectService $projectService
    ): void {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $this->ticketService = $ticketService;
        $this->projectService = $projectService;
    }

    /**
     * @throws BindingResolutionException
     */
    public function get($params): Response
    {
        $ticketId = $params['id'] ?? '';

        $ticket = $this->ticketService->getTicket($ticketId);

        if (! $ticket) {
            return $this->tpl->displayPartial('errors.error404', responseCode: 404);
        }

        $projects = $this->projectService->getProjectsAssignedToUser(session('userdata.id'));

        $this->tpl->assign('ticket', $ticket);
        $this->tpl->assign('projects', $projects);

        return $this->tpl->displayPartial('tickets.moveTicket');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {
        if (! empty($ticketId = (int) $_GET['id'] ?? null) && ! empty($projectId = (int) $params['projectId'] ?? null)) {
            if ($this->ticketService->moveTicket($ticketId, $projectId)) {
                $this->tpl->setNotification($this->language->__('text.ticket_moved'), 'success');
            } else {
                $this->tpl->setNotification($this->language->__('text.move_problem'), 'error');
            }
        }

        return FrontcontrollerCore::redirect(BASE_URL.'/tickets/moveTicket/'.$ticketId.'?closeModal=true');
    }
}
