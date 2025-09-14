<?php

namespace Safe4Work\Domain\Install\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller as FrontcontrollerCore;
use Safe4Work\Domain\Install\Repositories\Install as InstallRepository;
use Symfony\Component\HttpFoundation\Response;

class Index extends Controller
{
    private InstallRepository $installRepo;

    /**
     * init - initialize private variables
     *
     * @throws HttpResponseException
     */
    public function init(InstallRepository $installRepo)
    {
        $this->installRepo = $installRepo;

        if ($this->installRepo->checkIfInstalled()) {
            return FrontcontrollerCore::redirect(BASE_URL.'/');
        }
    }

    /**
     * get - handle get requests
     *
     * @param  $params  parameters or body of the request
     */
    public function get($params)
    {
        return $this->tpl->display('install.new', 'entry');
    }

    public function post($params): Response
    {
        $values = [
            'email' => '',
            'password' => '',
            'firstname' => '',
            'lastname' => '',
        ];

        if (isset($_POST['install'])) {
            $values = [
                'email' => ($params['email']),
                'firstname' => ($params['firstname']),
                'lastname' => ($params['lastname']),
                'company' => ($params['company']),
            ];

            $notificationSet = false; // Track whether a notification has been set

            if (empty($params['email'])) {
                $this->tpl->setNotification('notification.enter_email', 'error');
                $notificationSet = true;
            }

            if (empty($params['firstname']) && ! $notificationSet) {
                $this->tpl->setNotification('notification.enter_firstname', 'error');
                $notificationSet = true;
            }

            if (empty($params['lastname']) && ! $notificationSet) {
                $this->tpl->setNotification('notification.enter_lastname', 'error');
                $notificationSet = true;
            }

            if (empty($params['company']) && ! $notificationSet) {
                $this->tpl->setNotification('notification.enter_company', 'error');
                $notificationSet = true;
            }

            if (! $notificationSet) {
                // No notifications were set, all fields are valid
                if ($this->installRepo->setupDB($values)) {

                    $this->tpl->setNotification(sprintf($this->language->__('notifications.installation_success_setup_account'), BASE_URL), 'success');

                    if (session()->has('pwReset')) {
                        return FrontcontrollerCore::redirect(BASE_URL.'/auth/userInvite/'.session('pwReset'));
                    }

                } else {
                    $this->tpl->setNotification($this->language->__('notification.error_installing'), 'error');
                }
            }
        }

        return FrontcontrollerCore::redirect(BASE_URL.'/install');
    }
}
