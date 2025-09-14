<?php

namespace Safe4Work\Views\Composers;

use Safe4Work\Core\Configuration\AppSettings;
use Safe4Work\Core\Configuration\Environment;
use Safe4Work\Core\UI\Composer;

class PageBottom extends Composer
{
    /**
     * @var array|string[]
     */
    public static array $views = [
        'global::sections.pageBottom',
    ];

    protected AppSettings $settings;

    protected Environment $environment;

    public function init(AppSettings $settings, Environment $environment): void
    {
        $this->settings = $settings;
        $this->environment = $environment;
    }

    public function with(): array
    {
        return [
            'version' => $this->settings->appVersion,
            'poorMansCron' => $this->environment->get('poorMansCron'),
            'loggedIn' => session()->exists('userdata'),
        ];
    }
}
