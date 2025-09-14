<?php

namespace Safe4Work\Domain\Connector\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Connector\Services;

class Show extends Controller
{
    private Services\Providers $providerService;

    /**
     * constructor - initialize private variables
     */
    public function init(Services\Providers $projectService)
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);
        $this->providerService = $projectService;
    }

    /**
     * get - handle get requests
     */
    public function get($params)
    {
        $providers = $this->providerService->getProviders();

        $this->tpl->assign('providers', $providers);

        return $this->tpl->display('connector.show');
    }

    /**
     * post - handle post requests
     */
    public function post($params)
    {
        // Redirect.
    }
}
