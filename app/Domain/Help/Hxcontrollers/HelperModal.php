<?php

namespace Safe4Work\Domain\Help\Hxcontrollers;

use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Help\Services\Helper;
use Safe4Work\Domain\Users\Services\Users as UserService;

class HelperModal extends HtmxController
{
    protected static string $view = '';

    protected Helper $helperService;

    protected UserService $userService;

    /**
     * Controller constructor
     *
     * @param  \Leantime\Domain\Projects\Services\Projects  $projectService  The projects domain service.
     * @return void
     */
    public function init(
        Helper $helperService,
        UserService $userService
    ) {
        $this->helperService = $helperService;
        $this->userService = $userService;
    }

    public function get() {}

    public function dontShowAgain($params)
    {

        $modal = $params['modalId'] ?? '';
        $hidePermanently = ($params['hidePermanently'] ?? false) === 'on' ? true : false;

        if ($modal !== '') {
            $this->userService->updateUserSettings('modals', $modal, $hidePermanently);
        }

        return $this->tpl->emptyResponse();
    }
}
