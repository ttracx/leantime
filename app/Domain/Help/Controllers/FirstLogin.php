<?php

namespace Safe4Work\Domain\Help\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Help\Contracts\OnboardingSteps;
use Safe4Work\Domain\Help\Services\Helper;
use Safe4Work\Domain\Setting\Repositories\Setting;

class FirstLogin extends Controller
{
    private Helper $helperService;

    public function init(Helper $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * get - handle get requests
     */
    public function get($params)
    {

        $allSteps = $this->helperService->getFirstLoginSteps();

        $currentStepKey = collect($allSteps)->keys()->first();

        if (isset($_GET['step']) && $_GET['step'] == 'end') {
            $content = '  <script>
                    confetti();
                    jQuery.nmTop().close(2000);
                </script>';

            return new \Illuminate\Http\Response($content);
        }

        if (isset($_GET['step']) && isset($allSteps[$_GET['step']])) {
            $currentStepKey = (int) $_GET['step'];
        }

        $currentStep = $allSteps[$currentStepKey];

        /** @var OnboardingSteps $stepObject */
        $nextStepObject = app()->make($currentStep['class']);

        $this->tpl->assign('currentStep', $currentStepKey);
        $this->tpl->assign('nextStep', $currentStep['next']);

        return $this->tpl->displayPartial($nextStepObject->getTemplate());
    }

    /**
     * post - handle post requests
     */
    public function post($params)
    {
        $settingsRepo = app()->make(Setting::class);

        $step = $params['currentStep'];

        $allSteps = $this->helperService->getFirstLoginSteps();

        if (isset($params['currentStep']) && is_numeric($params['currentStep']) && isset($allSteps[$params['currentStep']])) {
            $currentStep = $allSteps[$params['currentStep']];
        } else {
            return Frontcontroller::redirect(BASE_URL.'/help/firstLogin');
        }

        /** @var OnboardingSteps $stepObject */
        $currentStepObject = app()->make($currentStep['class']);

        $result = $currentStepObject->handle($params);

        if ($result) {
            return Frontcontroller::redirect(BASE_URL.'/help/firstLogin?step='.$currentStep['next']);
        }

        return Frontcontroller::redirect(BASE_URL.'/help/firstLogin?step='.$params['currentStep']);

    }
}
