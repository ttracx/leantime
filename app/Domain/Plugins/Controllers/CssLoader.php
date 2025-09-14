<?php

namespace Safe4Work\Domain\Plugins\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Plugins\Services\Plugins as PluginService;
use Symfony\Component\HttpFoundation\Response;

class CssLoader extends Controller
{
    private PluginService $pluginService;

    public function init(PluginService $pluginService): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin]);
        $this->pluginService = $pluginService;
    }

    public function get(): Response
    {
        $cssFiles = self::dispatch_filter('pluginCss', []);
        $cssStrs = collect($cssFiles)
            ->filter(fn ($file) => file_exists(APP_ROOT."/plugins/$file"))
            ->map(fn ($file) => file_get_contents(APP_ROOT."/plugins/$file"))
            ->all();

        $response = new Response(implode('', $cssStrs));
        $response->headers->set('Content-Type', 'text/css');

        return $response;
    }
}
