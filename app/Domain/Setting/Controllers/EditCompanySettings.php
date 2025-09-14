<?php

namespace Safe4Work\Domain\Setting\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\UI\Theme;
use Safe4Work\Domain\Api\Services\Api as ApiService;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Setting\Services\Setting as SettingService;

class EditCompanySettings extends Controller
{
    private SettingRepository $settingsRepo;

    private ApiService $APIService;

    private SettingService $settingsSvc;

    private Theme $theme;

    /**
     * constructor - initialize private variables
     */
    public function init(
        SettingRepository $settingsRepo,
        ApiService $APIService,
        SettingService $settingsSvc,
        Theme $theme,

    ) {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        $this->settingsRepo = $settingsRepo;
        $this->APIService = $APIService;
        $this->settingsSvc = $settingsSvc;
        $this->theme = $theme;
    }

    /**
     * get - handle get requests
     */
    public function get($params)
    {
        if (! Auth::userIsAtLeast(Roles::$owner)) {
            return $this->tpl->display('errors.error403', responseCode: 403);
        }

        if (isset($_GET['resetLogo'])) {
            $this->settingsSvc->resetLogo();

            return Frontcontroller::redirect(BASE_URL.'/setting/editCompanySettings#look');
        }

        $companySettings = [
            'logo' => $this->theme->getLogoUrl(),
            'primarycolor' => session('companysettings.primarycolor') ?? '',
            'secondarycolor' => session('companysettings.secondarycolor') ?? '',
            'name' => session('companysettings.sitename'),
            'language' => session('companysettings.language'),
            'telemetryActive' => true,
            'messageFrequency' => '',
        ];

        $mainColor = $this->settingsRepo->getSetting('companysettings.mainColor');
        if ($mainColor !== false) {
            $companySettings['primarycolor'] = '#'.$mainColor;
            $companySettings['secondarycolor'] = '#'.$mainColor;
        }

        $primaryColor = $this->settingsRepo->getSetting('companysettings.primarycolor');
        if ($primaryColor !== false) {
            $companySettings['primarycolor'] = $primaryColor;
        }

        $secondaryColor = $this->settingsRepo->getSetting('companysettings.secondarycolor');
        if ($secondaryColor !== false) {
            $companySettings['secondarycolor'] = $secondaryColor;
        }

        $sitename = $this->settingsRepo->getSetting('companysettings.sitename');
        if ($sitename !== false) {
            $companySettings['name'] = $sitename;
        }

        $language = $this->settingsRepo->getSetting('companysettings.language');
        if ($language !== false) {
            $companySettings['language'] = $language;
        }

        $messageFrequency = $this->settingsRepo->getSetting('companysettings.messageFrequency');
        if ($messageFrequency !== false) {
            $companySettings['messageFrequency'] = $messageFrequency;
        }

        $apiKeys = $this->APIService->getAPIKeys();

        $this->tpl->assign('apiKeys', $apiKeys);
        $this->tpl->assign('languageList', $this->language->getLanguageList());
        $this->tpl->assign('companySettings', $companySettings);

        return $this->tpl->display('setting.editCompanySettings');
    }

    /**
     * post - handle post requests
     */
    public function post($params)
    {
        // Look & feel updates
        if (isset($params['primarycolor']) && $params['primarycolor'] != '') {
            $this->settingsRepo->saveSetting('companysettings.primarycolor', htmlentities(addslashes($params['primarycolor'])));
            $this->settingsRepo->saveSetting('companysettings.secondarycolor', htmlentities(addslashes($params['secondarycolor'])));

            // Check if main color is still in the system
            // if so remove. This call should be removed in a few versions.
            $mainColor = $this->settingsRepo->getSetting('companysettings.mainColor');
            if ($mainColor !== false) {
                $this->settingsRepo->deleteSetting('companysettings.mainColor');
            }

            session(['companysettings.primarycolor' => htmlentities(addslashes($params['primarycolor']))]);
            session(['companysettings.secondarycolor' => htmlentities(addslashes($params['secondarycolor']))]);

            $this->tpl->setNotification($this->language->__('notifications.company_settings_edited_successfully'), 'success');
        }

        // Main Details
        if (isset($params['name']) && $params['name'] != '' && isset($params['language']) && $params['language'] != '') {
            $this->settingsRepo->saveSetting('companysettings.sitename', htmlspecialchars(addslashes($params['name'])));
            $this->settingsRepo->saveSetting('companysettings.language', htmlentities(addslashes($params['language'])));
            $this->settingsRepo->saveSetting('companysettings.messageFrequency', (int) $params['messageFrequency']);

            session(['companysettings.sitename' => htmlspecialchars(addslashes($params['name']))]);
            session(['companysettings.language' => htmlentities(addslashes($params['language']))]);

            if (isset($_POST['telemetryActive'])) {
                $this->settingsRepo->saveSetting('companysettings.telemetry.active', 'true');
            } else {
                // Set remote telemetry to false:
                app()->make(ReportService::class)->optOutTelemetry();
            }

            $this->tpl->setNotification($this->language->__('notifications.company_settings_edited_successfully'), 'success');
        }

        return Frontcontroller::redirect(BASE_URL.'/setting/editCompanySettings');
    }

    /**
     * put - handle put requests
     */
    public function put($params) {}

    /**
     * delete - handle delete requests
     */
    public function delete($params) {}
}
