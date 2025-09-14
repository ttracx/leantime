<?php

namespace Unit\app\Core\UI;

use Safe4Work\Core\Configuration\AppSettings;
use Safe4Work\Core\Configuration\Environment;
use Safe4Work\Core\Files\FileManager;
use Safe4Work\Core\Language;
use Safe4Work\Core\UI\Theme;
use Safe4Work\Domain\Setting\Repositories\Setting;

class ThemeTest extends \Unit\TestCase
{
    use \Codeception\Test\Feature\Stub;

    /**
     * The test object
     *
     * @var Theme
     */
    protected $theme;

    protected $settingsRepoMock;

    protected $languageMock;

    protected $configMock;

    protected $appSettingsMock;

    protected $fileManagerMock;

    protected function setUp(): void
    {

        parent::setUp();

        if (! defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost');
        }

        $this->settingsRepoMock = $this->make(Setting::class, [

        ]);
        $this->languageMock = $this->make(Language::class, [

        ]);

        $this->fileManagerMock = $this->make(FileManager::class, [

        ]);

        $this->configMock = $this->make(Environment::class, [
            'primarycolor' => '#123',
            'secondarycolor' => '#123',

        ]);

        $this->appSettingsMock = $this->make(AppSettings::class, [
            'appVersion' => '123',
        ]);

    }

    protected function _after()
    {
        $this->theme = null;
    }

    // Write tests below

    /**
     * Test GetMenuTypes method
     */
    public function test_get_default_color_scheme_with_color_env_set()
    {

        // Load class to be tested
        $this->theme = new Theme(
            settingsRepo: $this->settingsRepoMock,
            language: $this->languageMock,
            config: $this->configMock,
            appSettings: $this->appSettingsMock,
            fileManager: $this->fileManagerMock
        );

        $colorScheme = $this->theme->getColorScheme();
        $this->assertEquals('companyColors', $colorScheme);

    }

    /**
     * Test GetMenuTypes method
     */
    public function test_get_default_color_scheme_without_env()
    {

        $configMock = $this->make(Environment::class, []);

        $theme = new Theme(
            settingsRepo: $this->settingsRepoMock,
            language: $this->languageMock,
            config: $configMock,
            appSettings: $this->appSettingsMock,
            fileManager: $this->fileManagerMock
        );

        $colorScheme = $theme->getColorScheme();
        $this->assertEquals('themeDefault', $colorScheme);

    }
}
