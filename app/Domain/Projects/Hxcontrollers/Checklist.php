<?php

namespace Safe4Work\Domain\Projects\Hxcontrollers;

use Error;
use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Projects\Services\Projects;

class Checklist extends HtmxController
{
    protected static string $view = 'projects::partials.checklist';

    private Projects $projectService;

    /**
     * Controller constructor
     *
     * @param  Projects  $projectService  The projects domain service.
     */
    public function init(Projects $projectService): void
    {
        $this->projectService = $projectService;
    }

    /**
     * Updates subtask status
     *
     * @throws BindingResolutionException
     */
    public function updateSubtask(): void
    {
        if (! $this->incomingRequest->getMethod() == 'PATCH') {
            throw new Error('This endpoint only supports PATCH requests');
        }

        // update project progress
        $projectProgress = $this->incomingRequest->request->all();

        $this->projectService->updateProjectProgress($projectProgress, session('currentProject'));

        // return view with new data
        [$progressSteps, $percentDone] = $this->projectService->getProjectSetupChecklist(session('currentProject'));
        $this->tpl->assign('progressSteps', $progressSteps);
        $this->tpl->assign('percentDone', $percentDone);
        $this->tpl->assign('includeTitle', false);
    }
}
