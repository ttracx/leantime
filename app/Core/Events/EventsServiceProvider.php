<?php

namespace Safe4Work\Core\Events;

use Illuminate\Support\ServiceProvider;
use Safe4Work\Core;

class EventsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('events', function ($app) {
            return new Core\Events\EventDispatcher;
        });

        $this->booting(function () {

            // Core\Events\EventDispatcher::discover_listeners();

            /*

            foreach ($this->subscribe as $subscriber) {
                Event::subscribe($subscriber);
            }

            foreach ($this->observers as $model => $observers) {
                $model::observe($observers);
            }*/

        });

        /*
        $this->booted(function () {
            $this->configureEmailVerification();
        });
        */

    }

    public function boot() {}
}
