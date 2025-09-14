<?php

namespace Safe4Work\Domain\Calendar\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * importGCal Class - Add a new client
 */
class ImportGCal extends Controller
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
     */
    public function run(): Response
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $values = [
            'url' => '',
            'name' => '',
            'colorClass' => '',
        ];

        if (isset($_POST['name']) === true || isset($_POST['url']) === true) {
            $values = [
                'url' => ($_POST['url']),
                'name' => ($_POST['name'] ?? 'My Calendar'),
                'colorClass' => ($_POST['colorClass'] ?? '#082236'),
            ];

            $this->calendarRepo->addGUrl($values);
            $this->tpl->setNotification('notification.gcal_imported_successfully', 'success', 'externalcalendar_created');
        }

        $this->tpl->assign('values', $values);

        return $this->tpl->displayPartial('calendar.importGCal');
    }
}
