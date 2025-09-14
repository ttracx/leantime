<?php

namespace Safe4Work\Core\Sessions;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Session\SessionServiceProvider as LaravelSessionServiceProvider;
use Safe4Work\Core\Middleware\StartSession;

class SessionServiceProvider extends LaravelSessionServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSessionManager();

        $this->registerSessionDriver();

        $this->app->singleton(StartSession::class, function ($app) {
            return new StartSession($app->make(SessionManager::class), function () use ($app) {
                return $app->make(CacheFactory::class);
            });
        });

    }

    /**
     * Register the session manager instance.
     *
     * @return void
     *
     * @override
     */
    protected function registerSessionManager()
    {

        $this->app->singleton('session', function ($app) {

            // Switch to redis as session store when setting useRedis is set
            if (! empty($app['config']['useRedis']) && (bool) $app['config']['useRedis'] === true) {

                $app['config']->set('session.driver', 'redis');
                $app['config']->set('session.connection', 'sessions');

            } else {

                // Ensure session base path exists:
                if (! is_dir(storage_path('framework/sessions')) && ! mkdir(
                    $concurrentDirectory = storage_path('framework/sessions'),
                    0755,
                    true
                ) && ! is_dir(
                    $concurrentDirectory
                )) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }

            }

            return new \Illuminate\Session\SessionManager($app);
        });
    }
}
