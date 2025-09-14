<?php

namespace Safe4Work\Domain\Calendar\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Symfony\Component\HttpFoundation\Response;

class ShowAllGCals extends Controller
{
    private CalendarRepository $calendarRepo;

    /**
     * init - initialize private variables
     */
    public function init(CalendarRepository $calendarRepo): void
    {
        $this->calendarRepo = $calendarRepo;
    }

    /**
     * run - display template and edit data
     *
     *
     *
     * @throws \Exception
     */
    public function run(): Response
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        // Assign vars
        $this->tpl->assign('allCalendars', $this->calendarRepo->getMyGoogleCalendars());

        return $this->tpl->display('calendar.showAllGCals');
    }
}
