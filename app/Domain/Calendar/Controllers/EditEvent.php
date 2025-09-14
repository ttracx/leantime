<?php

/**
 * editEvent Class - Add a new client
 */

namespace Safe4Work\Domain\Calendar\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\Support\FromFormat;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Calendar\Services\Calendar as CalendarService;
use Symfony\Component\HttpFoundation\Response;

class EditEvent extends Controller
{
    private CalendarService $calendarService;

    /**
     * init - initialize private variables
     */
    public function init(CalendarService $calendarService): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
        $this->calendarService = $calendarService;
    }

    /**
     * retrieves edit calendar event page data
     */
    public function get(array $params): Response
    {
        $values = $this->calendarService->getEvent($params['id']);

        $this->tpl->assign('values', $values);

        return $this->tpl->displayPartial('calendar.editEvent');
    }

    /**
     * sets, creates, and updates edit calendar event page data
     */
    public function post(array $params): Response
    {
        $params['id'] = $_GET['id'] ?? null;

        // Time comes in as 24:00 time from html5 element. Make it user date format
        $params['timeFrom'] = format(value: $params['timeFrom'], fromFormat: FromFormat::User24hTime)->userTime24toUserTime();
        $params['timeTo'] = format(value: $params['timeTo'], fromFormat: FromFormat::User24hTime)->userTime24toUserTime();

        $result = $this->calendarService->editEvent($params);

        if ($result === true) {
            $this->tpl->setNotification('notification.event_edited_successfully', 'success');
        } else {
            $this->tpl->setNotification('notification.please_enter_title', 'error');
        }

        return Frontcontroller::redirect(BASE_URL.'/calendar/editEvent/'.$params['id']);
    }
}
