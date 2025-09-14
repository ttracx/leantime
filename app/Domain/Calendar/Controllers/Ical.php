<?php

/**
 * showAll Class - show My Calender
 */

namespace Safe4Work\Domain\Calendar\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Calendar\Services\Calendar;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Ical extends Controller
{
    private Calendar $calendarService;

    /**
     * init - initialize private variables
     *
     * @param  Calendar  $calendarRepo
     */
    public function init(Calendar $calendarService): void
    {
        $this->calendarService = $calendarService;
    }

    /**
     * run - display template and edit data
     *
     *
     *
     * @throws BindingResolutionException
     */
    public function run($params): RedirectResponse|Response
    {

        // calendar id is not a standardized format. We'll have to parse it out
        // format is calendar.ical.CALENDARID
        $actParts = explode('.', $params['act'] ?? '');

        if (is_array($actParts) && count($actParts) === 3) {
            $calId = $actParts[2];
            $idParts = explode('_', $calId);
        } else {
            $calId = $_GET['id'] ?? '';
            $idParts = explode('_', $calId);
        }

        if (count($idParts) != 2) {
            return Frontcontroller::redirect(BASE_URL.'/errors/404');
        }

        try {

            $calendar = $this->calendarService->getIcalByHash($idParts[1], $idParts[0]);

            return new Response($calendar->get(), 200, [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="leantime-calendar.ics"',
            ]);

        } catch (\Exception $e) {
            return Frontcontroller::redirect(BASE_URL.'/errors/404');
        }

    }
}
