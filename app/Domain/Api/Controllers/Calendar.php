<?php

namespace Safe4Work\Domain\Api\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth as AuthService;
use Safe4Work\Domain\Calendar\Services\Calendar as CalendarService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Calendar controller
 */
class Calendar extends Controller
{
    private CalendarService $calendarSvc;

    /**
     * init - initialize private variables
     */
    public function init(CalendarService $calendarSvc): void
    {
        $this->calendarSvc = $calendarSvc;
    }

    /**
     * get - handle get requests
     */
    public function get(): Response
    {
        return $this->tpl->displayJson(['status' => 'Not implemented'], 501);
    }

    /**
     * post - handle post requests
     */
    public function post(array $params): Response
    {
        return $this->tpl->displayJson(['status' => 'Not implemented'], 501);
    }

    /**
     * patch - handle patch requests
     */
    public function patch(array $params): Response
    {
        if (! AuthService::userIsAtLeast(Roles::$editor)) {
            return $this->tpl->displayJson(['status' => 'failure', 'message' => 'Not authorized'], 401);
        }

        if (! isset($params['id'])) {
            return $this->tpl->displayJson(['status' => 'failure', 'message' => 'ID not set'], 400);
        }

        if (! $this->calendarSvc->patch($params['id'], $params)) {
            return $this->tpl->displayJson(['status' => 'failure'], 500);
        }

        return $this->tpl->displayJson(['status' => 'ok']);
    }

    /**
     * delete - handle delete requests
     */
    public function delete(array $params): Response
    {
        return $this->tpl->displayJson(['status' => 'Not implemented'], 501);
    }
}
