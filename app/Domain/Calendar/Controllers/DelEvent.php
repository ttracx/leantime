<?php

/**
 * delClient Class - Deleting clients
 */

namespace Safe4Work\Domain\Calendar\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Calendar\Services\Calendar as CalendarService;
use Symfony\Component\HttpFoundation\Response;

class DelEvent extends Controller
{
    private CalendarService $calendarService;

    /**
     * init - initialize private variables
     */
    public function init(CalendarService $calendarService): void
    {
        $this->calendarService = $calendarService;
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
    }

    /**
     * retrieves delete calendar event page data
     */
    public function get(array $params): Response
    {
        return $this->tpl->displayPartial('calendar.delEvent');
    }

    /**
     * sets, creates, and updates edit calendar event page data
     */
    public function post(array $params): Response
    {

        if (isset($_GET['id']) === false) {
            return Frontcontroller::redirect(BASE_URL.'/calendar/showMyCalendar/');
        }

        $id = (int) $_GET['id'];
        $result = $this->calendarService->delEvent($id);

        if (is_numeric($result) === true) {
            $this->tpl->setNotification('notification.event_removed_successfully', 'success');

            return Frontcontroller::redirect(BASE_URL.'/calendar/showMyCalendar/');
        } else {
            $this->tpl->setNotification('notification.could_not_delete_event', 'error');

            return $this->tpl->displayPartial('calendar.delEvent');
        }
    }
}
