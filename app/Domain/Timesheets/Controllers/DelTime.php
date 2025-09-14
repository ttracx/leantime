<?php

namespace Safe4Work\Domain\Timesheets\Controllers;

use Illuminate\Http\RedirectResponse;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Timesheets\Repositories\Timesheets as TimesheetRepository;
use Symfony\Component\HttpFoundation\Response;

class DelTime extends Controller
{
    private TimesheetRepository $timesheetsRepo;

    /**
     * init - initialize private variable
     */
    public function init(TimesheetRepository $timesheetsRepo): void
    {
        $this->timesheetsRepo = $timesheetsRepo;
    }

    /**
     * run - display template and edit data
     */
    public function run(): Response|RedirectResponse
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor], true);

        if (isset($_GET['id']) === true) {
            $id = (int) ($_GET['id']);

            if (isset($_POST['del']) === true) {
                $this->timesheetsRepo->deleteTime($id);

                $this->tpl->setNotification('notifications.time_deleted_successfully', 'success');

                if (session()->exists('lastPage')) {
                    return Frontcontroller::redirect(session('lastPage'));
                } else {
                    return Frontcontroller::redirect(BASE_URL.'/timsheets/showMyList');
                }
            }

            $this->tpl->assign('id', $id);

            return $this->tpl->displayPartial('timesheets.delTime');
        } else {
            return $this->tpl->displayPartial('errors.error403');
        }
    }
}
