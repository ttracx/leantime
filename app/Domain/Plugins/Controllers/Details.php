<?php

namespace Safe4Work\Domain\Plugins\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Plugins\Services\Plugins as PluginService;
use Symfony\Component\HttpFoundation\Response;

class Details extends Controller
{
    private PluginService $pluginService;

    public function init(PluginService $pluginService): void
    {
        $this->pluginService = $pluginService;
    }

    public function get(): Response
    {

        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        if (! $this->incomingRequest->query->has('id')) {
            throw new \Exception('Plugin Identifier is required');
        }

        /**
         * @var \Leantime\Domain\Plugins\Models\MarketplacePlugin|false $plugin
         */
        $plugin = $this->pluginService->getMarketplacePlugin(
            $this->incomingRequest->query->get('id'),
        );

        if (! $plugin) {
            return $this->tpl->display('errors.error404', 'blank');
        }

        $isBundle = false;
        if (collect($plugin->categories)->where('slug', '=', 'bundles')->count() > 0) {
            $isBundle = true;
        }

        $this->tpl->assign('isBundle', $isBundle);
        $this->tpl->assign('plugin', $plugin);

        return $this->tpl->display('plugins.plugindetails', 'blank');
    }
}
