<?php

namespace Safe4Work\Domain\Plugins\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Plugins\Services\Plugins as PluginService;
use Symfony\Component\HttpFoundation\Response;

class Marketplace extends Controller
{
    private PluginService $pluginService;

    public function init(
        PluginService $pluginService,
    ): void {
        $this->pluginService = $pluginService;
    }

    public function get(): Response
    {

        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        $this->tpl->assign('plugins', []);

        return $this->tpl->display('plugins.marketplace');
    }
}
