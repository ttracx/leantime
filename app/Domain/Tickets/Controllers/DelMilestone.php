<?php

namespace Safe4Work\Domain\Tickets\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Symfony\Component\HttpFoundation\Response;

class DelMilestone extends Controller
{
    private TicketService $ticketService;

    public function init(TicketService $ticketService): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $this->ticketService = $ticketService;
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(): Response
    {

        // Only admins
        if (Auth::userIsAtLeast(Roles::$editor)) {
            if (isset($_GET['id'])) {
                $id = (int) ($_GET['id']);
            }

            $this->tpl->assign('ticket', $this->ticketService->getTicket($id));

            return $this->tpl->displayPartial('tickets.delMilestone');
        } else {
            return $this->tpl->displayPartial('errors.error403');
        }
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {
        if (! isset($_GET['id'], $params['del'])) {
            return $this->tpl->displayPartial('errors.error400', responseCode: 400);
        }

        if (! Auth::userIsAtLeast(Roles::$editor)) {
            return $this->tpl->displayPartial('errors.error403', responseCode: 403);
        }

        if ($result = $this->ticketService->deleteMilestone($id = (int) ($_GET['id']))) {
            $this->tpl->setNotification($this->language->__('notification.milestone_deleted'), 'success');

            return Frontcontroller::redirect(BASE_URL.'/tickets/roadmap');
        }

        $this->tpl->setNotification($this->language->__($result['msg']), 'error');
        $this->tpl->assign('ticket', $this->ticketService->getTicket($id));

        return $this->tpl->displayPartial('tickets.delMilestone');
    }
}
