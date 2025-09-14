<?php

namespace Safe4Work\Domain\Help\Services;

use Safe4Work\Core\Events\DispatchesEvents;
use Safe4Work\Domain\Help\Contracts\OnboardingSteps;
use Safe4Work\Domain\Projects\Services\Projects;
use Safe4Work\Domain\Setting\Repositories\Setting;
use Safe4Work\Domain\Tickets\Services\Tickets;

class FirstTaskStep implements OnboardingSteps
{
    use DispatchesEvents;

    public function __construct(
        private Setting $settingsRepo,
        private Projects $projectService,
        private Tickets $ticketService
    ) {}

    /**
     * Get the title of the current project.
     *
     * @return string The title of the current project.
     */
    public function getTitle(): string
    {
        return 'Name your first task';
    }

    /**
     * Get the action for the current request.
     *
     * @return string The action for the current request.
     */
    public function getAction(): string
    {
        // TODO: Implement getAction() method.
        return 'ProjectIntro';
    }

    /**
     * Retrieves the template for the project introduction step.
     *
     * @return string The template name for the project introduction step.
     */
    public function getTemplate(): string
    {
        return 'help.firstTaskStep';
    }

    /**
     * Handle the given parameters.
     *
     * @param  array  $params  The parameters passed to the handle method.
     * @return bool Returns true on success.
     */
    public function handle($params): bool
    {

        if (isset($params['headline'])) {
            $this->ticketService->quickAddTicket(['headline' => $params['headline']], session('currentProject'));
        }

        $this->settingsRepo->saveSetting('user.'.session()?->get('userdata.id', -1).'.firstLoginCompleted', true);

        return true;
    }
}
