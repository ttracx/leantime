<?php

namespace Safe4Work\Domain\Connector\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;

class Providers extends Controller
{
    /**
     * constructor - initialize private variables
     */
    public function init()
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
    }

    /**
     * get - handle get requests
     */
    public function get($params)
    {
        return $this->tpl->displayPartial('connectors.providers');
    }

    /**
     * post - handle post requests
     */
    public function post($params)
    {
        return $this->tpl->displayPartial('connectors.providers');
    }
}
