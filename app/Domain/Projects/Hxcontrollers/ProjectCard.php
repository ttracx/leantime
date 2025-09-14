<?php

namespace Safe4Work\Domain\Projects\Hxcontrollers;

use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Calendar\Repositories\Calendar as CalendarRepository;
use Safe4Work\Domain\Clients\Repositories\Clients;
use Safe4Work\Domain\Comments\Services\Comments;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Reactions\Services\Reactions;
use Safe4Work\Domain\Reports\Services\Reports as ReportService;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Safe4Work\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Safe4Work\Domain\Users\Services\Users as UserService;

class ProjectCard extends HtmxController
{
    protected static string $view = 'projects::partials.projectCard';

    private ProjectService $projectsService;

    private TicketService $ticketsService;

    private UserService $usersService;

    private TimesheetService $timesheetsService;

    private ReportService $reportsService;

    private SettingRepository $settingRepo;

    private CalendarRepository $calendarRepo;

    private Clients $clientRepo;

    private Comments $commentsService;

    private Menu $menuService;

    private Reactions $reactionService;

    /**
     * Controller constructor
     *
     * @param  \Leantime\Domain\Projects\Services\Projects  $projectService  The projects domain service.
     * @return void
     */
    public function init(
        ProjectService $projectsService,
        TicketService $ticketsService,
        UserService $usersService,
        TimesheetService $timesheetsService,
        ReportService $reportsService,
        SettingRepository $settingRepo,
        CalendarRepository $calendarRepo,
        Clients $clientRepo,
        Comments $commentsService,
        Menu $menuService,
        Reactions $reactionService
    ) {
        $this->projectsService = $projectsService;
        $this->ticketsService = $ticketsService;
        $this->usersService = $usersService;
        $this->timesheetsService = $timesheetsService;
        $this->reportsService = $reportsService;
        $this->settingRepo = $settingRepo;
        $this->calendarRepo = $calendarRepo;
        $this->clientRepo = $clientRepo;
        $this->commentsService = $commentsService;
        $this->menuService = $menuService;
        $this->reactionService = $reactionService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }

    public function get() {}

    public function toggleFavorite()
    {

        $projectData = $this->incomingRequest->request->all();

        $projectId = $projectData['projectId'];
        $isFavorite = $projectData['isFavorite'];

        $project = $this->projectsService->getProject($projectId);

        if ($isFavorite) {
            $this->reactionService->removeReaction(
                userId: session('userdata.id'),
                module: 'project',
                moduleId: $projectId,
                reaction: 'favorite'
            );
        } else {
            $this->reactionService->addReaction(
                userId: session('userdata.id'),
                module: 'project',
                moduleId: $projectId,
                reaction: 'favorite'
            );
        }

        $this->tpl->setHTMXEvent('HTMX.updateProjectList');

        $project = $this->projectsService->getProject($projectId);
        $this->tpl->assign('project', $project);
    }

    public function getProgress()
    {

        $projectId = $_GET['projectId'];

        $project = ['id' => $projectId];

        $project['progress'] = $this->projectsService->getProjectProgress($project['id']);
        $projectComment = $this->commentsService->getComments('project', $project['id']);
        $project['team'] = $this->projectsService->getUsersAssignedToProject($project['id']);

        if (is_array($projectComment) && count($projectComment) > 0) {
            $project['lastUpdate'] = $projectComment[0];
        } else {
            $project['lastUpdate'] = false;
        }

        $projectTypeAvatars = $this->menuService->getProjectTypeAvatars();

        $currentUrlPath = BASE_URL.'/'.str_replace('.', '/', Frontcontroller::getCurrentRoute());

        $project = $this->projectsService->getProject($projectId);

        $this->tpl->assign('projectTypeAvatars', $projectTypeAvatars);
        $this->tpl->assign('currentUrlPath', $currentUrlPath);
        $this->tpl->assign('project', $project);
        $this->tpl->assign('type', 'full');
    }
}
