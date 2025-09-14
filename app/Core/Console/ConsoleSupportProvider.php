<?php

namespace Safe4Work\Core\Console;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Providers\ComposerServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;

class ConsoleSupportProvider extends ConsoleSupportServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        CliServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
