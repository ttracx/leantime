<?php

namespace Safe4Work\Domain\Help\Composers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Composer;
use Safe4Work\Core\Controller\Frontcontroller as FrontcontrollerCore;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Help\Services\Helper;
use Safe4Work\Domain\Setting\Repositories\Setting;

class Helpermodal extends Composer
{
    private Setting $settingsRepo;

    private Helper $helperService;

    private Auth $authService;

    public static array $views = [
        'help::helpermodal',
    ];

    public function init(
        Setting $settingsRepo,
        Helper $helperService,
        Auth $authService
    ): void {
        $this->settingsRepo = $settingsRepo;
        $this->helperService = $helperService;
        $this->authService = $authService;
    }

    /**
     * @throws BindingResolutionException
     */
    public function with(): array
    {
        $action = FrontcontrollerCore::getCurrentRoute();

        // Don't show modals in test environment
        if (app()->environment('testing')) {
            return ['showHelperModal' => false, 'currentModal' => [], 'isFirstLogin' => false];
        }

        $showHelperModal = false;
        $completedOnboarding = $this->settingsRepo->getSetting('companysettings.completedOnboarding');
        $isFirstLogin = $this->helperService->isFirstLogin($this->authService->getUserId());

        // Backwards compatibilty
        if ($isFirstLogin && $completedOnboarding) {
            $isFirstLogin = false;
        }

        $currentModal = $this->helperService->getHelperModalByRoute($action);

        if (
            $isFirstLogin === false
            && $currentModal['template'] !== 'notfound'
            && (
                session()->exists('usersettings.modals.'.$currentModal['template']) === false
                || session('usersettings.modals.'.$currentModal['template']) === false)
        ) {
            if (! session()->exists('usersettings.modals')) {
                session(['usersettings.modals' => []]);
            }

            if (! session()->exists('usersettings.modals.'.$currentModal['template'])) {
                session(['usersettings.modals.'.$currentModal['template'] => 1]);
                $showHelperModal = true;
            }
        }

        // For development purposes, always show the modal
        return [
            'completedOnboarding' => $completedOnboarding,
            'showHelperModal' => $showHelperModal,
            'currentModal' => is_array($currentModal) ? $currentModal['template'] : $currentModal,
            'isFirstLogin' => $isFirstLogin,
        ];
    }
}
