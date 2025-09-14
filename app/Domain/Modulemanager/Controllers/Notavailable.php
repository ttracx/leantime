<?php

/**
 * Controller / Delete Canvas
 */

namespace Safe4Work\Domain\ModuleManager\Controllers;

use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\Events\DispatchesEvents;

class Notavailable
{
    public function run($params)
    {

        $redirect = BASE_URL.'errors/error404';
        $redirect = DispatchesEvents::dispatch_filter('notAvailableRedirect', $redirect, $params);

        return Frontcontroller::redirect($redirect);
    }
}
