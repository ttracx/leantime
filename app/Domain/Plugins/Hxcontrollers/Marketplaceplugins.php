<?php

namespace Safe4Work\Domain\Plugins\Hxcontrollers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Plugins\Models\MarketplacePlugin;
use Safe4Work\Domain\Plugins\Services\Plugins as PluginService;

class Marketplaceplugins extends HtmxController
{
    protected static string $view = 'plugins::partials.pluginlist';

    private PluginService $pluginService;

    public function init(
        PluginService $pluginService,
    ): void {
        $this->pluginService = $pluginService;
    }

    /**
     * @throws BindingResolutionException
     */
    public function getlist(): void
    {
        /** @var MarketplacePlugin[] $plugins */
        $plugins = $this->pluginService->getMarketplacePlugins(
            $this->incomingRequest->query->get('page', 1),
            $this->incomingRequest->query->get('search', ''),
        );

        $this->tpl->assign('plugins', $plugins);
    }

    public function getLatest()
    {
        /** @var MarketplacePlugin[] $plugins */
        $plugins = $this->pluginService->getLatestPluginUpdates(
            $this->incomingRequest->query->get('page', 1),
            $this->incomingRequest->query->get('search', ''),
        );

        $this->tpl->assign('plugins', $plugins);

        return $this->tpl->displayPartial('plugins::partials.latestPlugins');
    }

    public function search(): void {}
}
