<?php

namespace Safe4Work\Domain\Menu\Services;

use Safe4Work\Core\Events\DispatchesEvents;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Setting\Services\Setting;
use Safe4Work\Domain\Sprints\Services\Sprints as SprintService;
use Safe4Work\Domain\Timesheets\Repositories\Timesheets as TimesheetRepository;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users;

class Menu
{
    use DispatchesEvents;

    private ProjectService $projectService;

    private TimesheetService $timesheetService;

    private SprintService $sprintService;

    private Users $userService;

    private Setting $settingSvc;

    /**
     * @param  TimesheetRepository  $timesheetsRepo
     * @param  SettingRepository  $settingsRepo
     */
    public function __construct(
        ProjectService $projectService,
        TimesheetService $timesheetService,
        SprintService $sprintService,
        Users $userService,
        Setting $settingSvc
    ) {

        $this->projectService = $projectService;
        $this->timesheetService = $timesheetService;
        $this->sprintService = $sprintService;
        $this->userService = $userService;
        $this->settingSvc = $settingSvc;
    }

    public function getUserProjectList(int $userId, null|int|string $client = null): array
    {

        $allAssignedprojects =
        $allAvailableProjects =
        $recentProjects =
        $returnVars = [];

        $user = $this->userService->getUser($userId);

        $projects = $this->projectService->getProjectHierarchyAssignedToUser($userId, 'open', $client);
        $allAssignedprojects = $projects['allAssignedProjects'];
        $allAssignedprojectsHierarchy = $projects['allAssignedProjectsHierarchy'];
        $favoriteProjects = $projects['favoriteProjects'];

        // Filtered
        $projects = $this->projectService->getProjectHierarchyAvailableToUser($userId, 'open', empty($client) ? session('userdata.clientId') : $client);
        $allAvailableProjects = $projects['allAvailableProjects'];
        $allAvailableProjectsHierarchy = $projects['allAvailableProjectsHierarchy'];

        $clients = $this->projectService->getAllClientsAvailableToUser($userId, 'open', $client);

        $recent = $this->settingSvc->getSetting('usersettings.'.$userId.'.recentProjects');
        $recentArr = unserialize($recent);

        // Make sure the suer has access to the project
        if (is_array($recentArr) && is_array($allAvailableProjects)) {
            $availableProjectColumn = array_column($allAvailableProjects, 'id');
            foreach ($recentArr as $recentItem) {
                $found_key = array_search($recentItem, $availableProjectColumn);
                if ($found_key !== false) {
                    $recentProjects[] = $allAvailableProjects[$found_key];
                }
            }
        }

        $projectType = 'project';
        $project = [];
        if ($currentProjectId = $this->projectService->getCurrentProjectId()) {
            $project = $this->projectService->getProject($currentProjectId);

            $projectType = ($project !== false && isset($project['type']))
                ? $project['type']
                : 'project';

            if ($projectType != '' && $projectType != 'project') {
                $menuType = $projectType;
            } else {
                $menuType = \Leantime\Domain\Menu\Repositories\Menu::DEFAULT_MENU;
            }

            if ($project !== false && isset($project['clientId'])) {
                $currentClient = $project['clientId'];
            } else {
                $currentClient = '';
            }
        } else {
            $menuType = \Leantime\Domain\Menu\Repositories\Menu::DEFAULT_MENU;
            $currentClient = '';
        }

        return [
            'assignedProjects' => $allAssignedprojects,
            'availableProjects' => $allAvailableProjects,
            'assignedHierarchy' => $allAssignedprojectsHierarchy,
            'availableProjectsHierarchy' => $allAvailableProjectsHierarchy,
            'currentClient' => $currentClient,
            'menuType' => $menuType,
            'recentProjects' => $recentProjects,
            'projectType' => $projectType,
            'favoriteProjects' => $favoriteProjects,
            'clients' => $clients,
            'currentProject' => $project,

        ];
    }

    public function getProjectTypeAvatars(): array
    {

        $projectTypeAvatars = [
            'project' => 'avatar',
            'strategy' => 'fa fa-chess',
            'program' => 'fa fa-layer-group',
        ];

        return self::dispatch_filter('projectTypeAvatars', $projectTypeAvatars);
    }

    public function getProjectSelectorGroupingOptions(): array
    {

        $projectSelectGrouping =
            [
                'structure' => 'Group by Project Structure',
                'client' => 'Group by Client',
                'none' => 'No Grouping',
            ];

        return self::dispatch_filter('projectSelectorGrouping', $projectSelectGrouping);
    }
}
