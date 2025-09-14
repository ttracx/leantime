<?php

namespace Tests\Unit\App\Core;

use Safe4Work\Core\Application;
use Safe4Work\Core\Bootstrap\LoadConfig;
use Safe4Work\Core\Bootstrap\SetRequestForConsole;
use Safe4Work\Core\Configuration\Environment;

class ApplicationUrlTest extends \Unit\TestCase
{
    protected $app;

    protected $config;

    protected function setUp(): void
    {

        parent::setUp();

        $this->bootstrapApplication();

    }

    protected function bootstrapApplication()
    {

        $this->app = new Application(APP_ROOT);

        $this->app->bootstrapWith([LoadConfig::class, SetRequestForConsole::class]);
        $this->app->boot();

        $this->config = $this->app['config'];
    }

    public function test_base_url_is_set_correctly_from_config(): void
    {
        // Test default behavior
        $this->assertEquals('https://leantime-dev', BASE_URL);
        $this->assertEquals('https://leantime-dev', $this->config->get('app.url'));

        // Test with LEAN_APP_URL set
        putenv('LEAN_APP_URL=https://example.com');
        $_ENV['LEAN_APP_URL'] = 'https://example.com';

        // Reinitialize application to test new environment
        $this->bootstrapApplication();

        // dd($this->config);

        $this->assertEquals('https://example.com', $this->config->get('app.url'));
        $this->assertEquals('https://example.com', $this->config->get('appUrl'));
    }

    public function test_base_url_handles_trailing_slash(): void
    {

        $_ENV['LEAN_APP_URL'] = 'https://example.com/';

        $this->bootstrapApplication();

        $this->assertEquals('https://example.com', $this->config->get('app.url'));
        $this->assertEquals('https://example.com', $this->config->get('appUrl'));
    }

    public function test_base_url_handles_subdirectory(): void
    {

        $_ENV['LEAN_APP_URL'] = 'https://example.com/leantime';

        $this->bootstrapApplication();

        $this->assertEquals('https://example.com/leantime', $this->config->get('app.url'));
        $this->assertEquals('https://example.com/leantime', $this->config->get('appUrl'));
    }

    public function test_base_url_handles_port(): void
    {

        $_ENV['LEAN_APP_URL'] = 'https://example.com:8443';

        $this->bootstrapApplication();

        $this->assertEquals('https://example.com:8443', $this->config->get('app.url'));
        $this->assertEquals('https://example.com:8443', $this->config->get('appUrl'));
    }

    public function test_base_url_handles_reverse_proxy(): void
    {
        // Simulate reverse proxy headers
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'example.com';

        $_ENV['LEAN_APP_URL'] = 'https://example.com';

        $this->bootstrapApplication();

        $this->assertEquals('https://example.com', $this->config->get('app.url'));
        $this->assertEquals('https://example.com', $this->config->get('appUrl'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up environment
        putenv('LEAN_APP_URL');
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        unset($_SERVER['HTTP_X_FORWARDED_HOST']);
    }
}
