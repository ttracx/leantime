<?php

namespace Safe4Work\Core\Application;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Safe4Work\Core\Configuration\AppSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AboutCommand::add('Environment', [
            'Leantime App Version' => fn () => $this->app->make(AppSettings::class)->appVersion,
            'Leantime Db Version' => fn () => $this->app->make(AppSettings::class)->dbVersion,
        ]);

    }
}
