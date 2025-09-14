<?php

namespace Safe4Work\Views\Composers;

use Safe4Work\Core\Configuration\AppSettings;
use Safe4Work\Core\UI\Composer;

class Footer extends Composer
{
    public static array $views = [
        'global::sections.footer',
    ];

    protected AppSettings $settings;

    public function init(AppSettings $settings): void
    {
        $this->settings = $settings;
    }

    public function with(): array
    {
        return [
            'version' => $this->settings->appVersion,
        ];
    }
}
