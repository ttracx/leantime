<?php

namespace Safe4Work\Domain\Reports\Services;

use DateTime;
use DateTimeZone;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Safe4Work\Core\Configuration\AppSettings as AppSettingCore;
use Safe4Work\Core\Configuration\Environment as EnvironmentCore;
use Safe4Work\Core\Events\DispatchesEvents;
use Safe4Work\Core\UI\Template as TemplateCore;
use Safe4Work\Domain\Clients\Repositories\Clients as ClientRepository;
use Safe4Work\Domain\Comments\Repositories\Comments as CommentRepository;
use Safe4Work\Domain\Eacanvas\Repositories\Eacanvas as EacanvaRepository;
use Safe4Work\Domain\Goalcanvas\Repositories\Goalcanvas as GoalcanvaRepository;
use Safe4Work\Domain\Ideas\Repositories\Ideas as IdeaRepository;
use Safe4Work\Domain\Insightscanvas\Repositories\Insightscanvas as InsightscanvaRepository;
use Safe4Work\Domain\Leancanvas\Repositories\Leancanvas as LeancanvaRepository;
use Safe4Work\Domain\Minempathycanvas\Repositories\Minempathycanvas as MinempathycanvaRepository;
use Safe4Work\Domain\Obmcanvas\Repositories\Obmcanvas as ObmcanvaRepository;
use Safe4Work\Domain\Projects\Repositories\Projects as ProjectRepository;
use Safe4Work\Domain\Reactions\Repositories\Reactions;
use Safe4Work\Domain\Reports\Repositories\Reports as ReportRepository;
use Safe4Work\Domain\Retroscanvas\Repositories\Retroscanvas as RetroscanvaRepository;
use Safe4Work\Domain\Riskscanvas\Repositories\Riskscanvas as RiskscanvaRepository;
use Safe4Work\Domain\Sbcanvas\Repositories\Sbcanvas as SbcanvaRepository;
use Safe4Work\Domain\Setting\Repositories\Setting as SettingRepository;
use Safe4Work\Domain\Setting\Services\Setting as SettingsService;
use Safe4Work\Domain\Sprints\Repositories\Sprints as SprintRepository;
use Safe4Work\Domain\Swotcanvas\Repositories\Swotcanvas as SwotcanvaRepository;
use Safe4Work\Domain\Tickets\Repositories\Tickets as TicketRepository;
use Safe4Work\Domain\Timesheets\Repositories\Timesheets as TimesheetRepository;
use Safe4Work\Domain\Users\Repositories\Users as UserRepository;
use Safe4Work\Domain\Valuecanvas\Repositories\Valuecanvas as ValuecanvaRepository;
use Safe4Work\Domain\Wiki\Repositories\Wiki as WikiRepository;

/**
 * @api
 */
class Reports
{
    use DispatchesEvents;

    private TemplateCore $tpl;

    private AppSettingCore $appSettings;

    private EnvironmentCore $config;

    private ProjectRepository $projectRepository;

    private SprintRepository $sprintRepository;

    private ReportRepository $reportRepository;

    private SettingsService $settings;

    private TicketRepository $ticketRepository;

    /**
     * @param  SettingRepository  $settings
     */
    public function __construct(
        TemplateCore $tpl,
        AppSettingCore $appSettings,
        EnvironmentCore $config,
        ProjectRepository $projectRepository,
        SprintRepository $sprintRepository,
        ReportRepository $reportRepository,
        SettingsService $settings,
        TicketRepository $ticketRepository
    ) {
        $this->tpl = $tpl;
        $this->appSettings = $appSettings;
        $this->config = $config;
        $this->projectRepository = $projectRepository;
        $this->sprintRepository = $sprintRepository;
        $this->reportRepository = $reportRepository;
        $this->settings = $settings;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @throws BindingResolutionException
     *
     * @api
     */
    public function dailyIngestion(): void
    {
        $this->runIngestionForProject(session('currentProject'));
    }

    protected function runIngestionForProject(int $projectId): void
    {

        if (Cache::has('dailyReports-'.$projectId) === false || Cache::get('dailyReports-'.$projectId) < dtHelper()->dbNow()->endOfDay()) {

            // Check if the dailyingestion cycle was executed already. There should be one entry for backlog and one entry for current sprint (unless there is no current sprint
            // Get current Sprint Id, if no sprint available, dont run the sprint burndown

            $lastEntries = $this->reportRepository->checkLastReportEntries($projectId);

            // If we receive 2 entries we have a report already. If we have one entry then we ran the backlog one and that means there was no current sprint.
            if (count($lastEntries) == 0) {
                $currentSprint = $this->sprintRepository->getCurrentSprint($projectId);

                if ($currentSprint !== false) {
                    $sprintReport = $this->reportRepository->runTicketReport($projectId, $currentSprint->id);
                    if ($sprintReport !== false) {
                        $this->reportRepository->addReport($sprintReport);
                    }
                }

                $backlogReport = $this->reportRepository->runTicketReport($projectId, '');

                if ($backlogReport !== false) {

                    $this->reportRepository->addReport($backlogReport);

                    Cache::put('dailyReports-'.$projectId, dtHelper()->dbNow()->endOfDay(), 14400); // 4hours

                }
            }

        }
    }

    public function cronDailyIngestion(): void
    {
        $projects = $this->projectRepository->getAll();

        foreach ($projects as $project) {
            $this->runIngestionForProject($project['id']);
        }

    }

    /**
     * @api
     */
    public function getFullReport($projectId): false|array
    {
        return $this->reportRepository->getFullReport($projectId);
    }

    /**
     * @throws BindingResolutionException
     *
     * @api
     */
    public function getRealtimeReport($projectId, $sprintId): array|bool
    {
        return $this->reportRepository->runTicketReport($projectId, $sprintId);
    }

    /**
     * @api
     */
    public function getAnonymousTelemetry(
        IdeaRepository $ideaRepository,
        UserRepository $userRepository,
        ClientRepository $clientRepository,
        CommentRepository $commentsRepository,
        TimesheetRepository $timesheetRepo,
        EacanvaRepository $eaCanvasRepo,
        InsightscanvaRepository $insightsCanvasRepo,
        LeancanvaRepository $leanCanvasRepo,
        ObmcanvaRepository $obmCanvasRepo,
        RetroscanvaRepository $retrosCanvasRepo,
        GoalcanvaRepository $goalCanvasRepo,
        ValuecanvaRepository $valueCanvasRepo,
        MinempathycanvaRepository $minEmpathyCanvasRepo,
        RiskscanvaRepository $risksCanvasRepo,
        SbcanvaRepository $sbCanvasRepo,
        SwotcanvaRepository $swotCanvasRepo,
        WikiRepository $wikiRepo
    ): array {

        // Get anonymous company guid
        $companyId = $this->settings->getCompanyId();

        self::dispatch_event('beforeTelemetrySend', ['companyId' => $companyId]);

        $companyLang = $this->settings->getSetting('companysettings.language');
        if ($companyLang != '' && $companyLang !== false) {
            $currentLanguage = $companyLang;
        } else {
            $currentLanguage = $this->config->language;
        }

        $projectStatusCount = $this->getProjectStatusReport();

        $taskSentiment = $this->generateTicketReactionsReport();

        $telemetry = [
            'date' => '',
            'companyId' => $companyId,
            'env' => 'oss',
            'version' => $this->appSettings->appVersion,
            'language' => $currentLanguage,
            'numUsers' => $userRepository->getNumberOfUsers(),
            'lastUserLogin' => $userRepository->getLastLogin(),

            'numProjects' => $this->projectRepository->getNumberOfProjects(null, 'project'),
            'numProjectsGreen' => $projectStatusCount['green'] ?? 0,
            'numProjectsYellow' => $projectStatusCount['yellow'] ?? 0,
            'numProjectsRed' => $projectStatusCount['red'] ?? 0,
            'numProjectsNone' => $projectStatusCount['none'] ?? 0,

            'numStrategies' => $this->projectRepository->getNumberOfProjects(null, 'strategy'),
            'numPrograms' => $this->projectRepository->getNumberOfProjects(null, 'program'),
            'numClients' => $clientRepository->getNumberOfClients(),
            'numComments' => $commentsRepository->countComments(),
            'numMilestones' => $this->ticketRepository->getNumberOfMilestones(),
            'numTickets' => $this->ticketRepository->getNumberOfAllTickets(),

            'numBoards' => $ideaRepository->getNumberOfBoards(),

            'numIdeaItems' => $ideaRepository->getNumberOfIdeas(),
            'numHoursBooked' => $timesheetRepo->getHoursBooked(),

            'numResearchBoards' => $leanCanvasRepo->getNumberOfBoards(),
            'numResearchItems' => $leanCanvasRepo->getNumberOfCanvasItems(),

            'numRetroBoards' => $retrosCanvasRepo->getNumberOfBoards(),
            'numRetroItems' => $retrosCanvasRepo->getNumberOfCanvasItems(),

            'numGoalBoards' => $goalCanvasRepo->getNumberOfBoards(),
            'numGoalItems' => $goalCanvasRepo->getNumberOfCanvasItems(),

            'numValueCanvasBoards' => $valueCanvasRepo->getNumberOfBoards(),
            'numValueCanvasItems' => $valueCanvasRepo->getNumberOfCanvasItems(),

            'numMinEmpathyBoards' => $minEmpathyCanvasRepo->getNumberOfBoards(),
            'numMinEmpathyItems' => $minEmpathyCanvasRepo->getNumberOfCanvasItems(),

            'numOBMBoards' => $obmCanvasRepo->getNumberOfBoards(),
            'numOBMItems' => $obmCanvasRepo->getNumberOfCanvasItems(),

            'numSWOTBoards' => $swotCanvasRepo->getNumberOfBoards(),
            'numSWOTItems' => $swotCanvasRepo->getNumberOfCanvasItems(),

            'numSBBoards' => $sbCanvasRepo->getNumberOfBoards(),
            'numSBItems' => $sbCanvasRepo->getNumberOfCanvasItems(),

            'numRISKSBoards' => $risksCanvasRepo->getNumberOfBoards(),
            'numRISKSItems' => $risksCanvasRepo->getNumberOfCanvasItems(),

            'numEABoards' => $eaCanvasRepo->getNumberOfBoards(),
            'numEAItems' => $eaCanvasRepo->getNumberOfCanvasItems(),

            'numINSIGHTSBoards' => $insightsCanvasRepo->getNumberOfBoards(),
            'numINSIGHTSItems' => $insightsCanvasRepo->getNumberOfCanvasItems(),

            'numWikiBoards' => $wikiRepo->getNumberOfBoards(),
            'numWikiItems' => $wikiRepo->getNumberOfCanvasItems(),

            'numTaskSentimentAngry' => $taskSentiment['🤬'] ?? 0,
            'numTaskSentimentDisgust' => $taskSentiment['🤢'] ?? 0,
            'numTaskSentimentUnhappy' => $taskSentiment['🙁'] ?? 0,
            'numTaskSentimentNeutral' => $taskSentiment['😐'] ?? 0,
            'numTaskSentimentHappy' => $taskSentiment['🙂'] ?? 0,
            'numTaskSentimentLove' => $taskSentiment['😍'] ?? 0,
            'numTaskSentimenUnicorn' => $taskSentiment['🦄'] ?? 0,

            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'phpUname' => php_uname(),
            'isDocker' => $this->isRunningInDocker(),
            'phpSapiName' => php_sapi_name(),
            'phpOs' => PHP_OS ?? 'unknown',

        ];

        $telemetry = self::dispatch_filter('beforeReturnTelemetry', $telemetry);

        return $telemetry;
    }

    /**
     * @throws BindingResolutionException
     *
     * @api
     */
    public function sendAnonymousTelemetry(): bool|PromiseInterface
    {

        // Only send once a day

        $allowTelemetry = app('config')->allowTelemetry ?? true;

        if ($allowTelemetry === true) {
            $date_utc = new DateTime('now', new DateTimeZone('UTC'));
            $today = $date_utc->format('Y-m-d');
            $lastUpdate = $this->settings->getSetting('companysettings.telemetry.lastUpdate');

            if ($lastUpdate != $today) {
                $telemetry = app()->call([$this, 'getAnonymousTelemetry']);
                $telemetry['date'] = $today;

                // Do the curl
                $httpClient = new Client;

                try {

                    $data_string = json_encode($telemetry);

                    $promise = $httpClient->postAsync('https://telemetry.leantime.io', [
                        'form_params' => [
                            'telemetry' => $data_string,
                        ],
                        'timeout' => 480,
                    ])->then(function ($response) use ($today) {
                        $this->settings->saveSetting('companysettings.telemetry.lastUpdate', $today);
                    });

                    return $promise;

                } catch (\Exception $e) {
                    Log::error($e);

                    return false;
                }
            }
        }

        return false;
    }

    /**
     * @return false|void
     *
     * @throws Exception
     *
     * @api
     */
    public function optOutTelemetry()
    {
        $date_utc = new DateTime('now', new DateTimeZone('UTC'));
        $today = $date_utc->format('Y-m-d');

        $companyId = $this->settings->getCompanyId();

        $telemetry = [
            'date' => '',
            'companyId' => $companyId,
            'version' => $this->appSettings->appVersion,
            'language' => '',
            'numUsers' => 0,
            'lastUserLogin' => 0,
            'numProjects' => 0,
            'numClients' => 0,
            'numComments' => 0,
            'numMilestones' => 0,
            'numTickets' => 0,

            'numBoards' => 0,

            'numIdeaItems' => 0,
            'numHoursBooked' => 0,

            'numResearchBoards' => 0,
            'numResearchItems' => 0,

            'numRetroBoards' => 0,
            'numRetroItems' => 0,

            'numGoalBoards' => 0,
            'numGoalItems' => 0,

            'numValueCanvasBoards' => 0,
            'numValueCanvasItems' => 0,

            'numMinEmpathyBoards' => 0,
            'numMinEmpathyItems' => 0,

            'numOBMBoards' => 0,
            'numOBMItems' => 0,

            'numSWOTBoards' => 0,
            'numSWOTItems' => 0,

            'numSBBoards' => 0,
            'numSBItems' => 0,

            'numRISKSBoards' => 0,
            'numRISKSItems' => 0,

            'numEABoards' => 0,
            'numEAItems' => 0,

            'numINSIGHTSBoards' => 0,
        ];

        $telemetry['date'] = $today;

        // Do the curl
        $httpClient = new Client;

        try {
            $data_string = json_encode($telemetry);

            $promise = $httpClient->postAsync('https://telemetry.leantime.io', [
                'form_params' => [
                    'telemetry' => $data_string,
                ],
                'timeout' => 5,
            ])->then(function ($response) use ($today) {

                $this->settings->saveSetting('companysettings.telemetry.lastUpdate', $today);
                session(['skipTelemetry' => true]);
            });
        } catch (\Exception $e) {
            report($e);

            session(['skipTelemetry' => true]);

            return false;
        }

        $this->settings->saveSetting('companysettings.telemetry.active', false);

        session(['skipTelemetry' => true]);

        try {
            $promise->wait();
        } catch (\Exception $e) {
            report($e);
        }

    }

    /**
     * @return array
     *
     * @throws Exception
     *
     * @api
     */
    public function getProjectStatusReport()
    {

        $projectStatus = $this->projectRepository->getAll();

        $statusList = ['green' => 0, 'yellow' => 0, 'red' => 0, 'none' => 0];
        foreach ($projectStatus as $project) {
            if (isset($statusList[$project['status']])) {
                $statusList[$project['status']]++;
            } else {
                $statusList['none']++;
            }
        }

        return $statusList;
    }

    public function generateTicketReactionsReport()
    {
        $reactionsRepo = app()->make(Reactions::class);
        $collectedReactions = $reactionsRepo->getReactionsByModule('ticketSentiment');

        $reactions = [
            '🤬' => 0,
            '🤢' => 0,
            '🙁' => 0,
            '😐' => 0,
            '🙂' => 0,
            '😍' => 0,
            '🦄' => 0,
            'other' => 0,
        ];

        foreach ($collectedReactions as $reaction) {
            if (isset($reactions[$reaction['reaction']])) {
                $reactions[$reaction['reaction']] = $reactions[$reaction['reaction']] + $reaction['reactionCount'];
            }
        }

        return $reactions;
    }

    /**
     * Checks if Leantime is running in a Docker environment
     * Uses multiple detection methods and handles errors gracefully
     */
    private function isRunningInDocker(): bool
    {
        // Method 1: Check for /.dockerenv file
        try {
            if (is_file('/.dockerenv')) {
                return true;
            }
        } catch (\Exception $e) {
            // Silently fail if file access is restricted
        }

        // Method 2: Check for Docker-specific environment variables
        if (getenv('DOCKER_CONTAINER') !== false || getenv('IS_DOCKER') !== false) {
            return true;
        }

        // Method 3: Check cgroup info (works on Linux hosts)
        try {
            return strpos(file_get_contents('/proc/1/cgroup'), 'docker') !== false;
        } catch (\Exception $e) {
            return false; // Return false if all detection methods fail
        }
    }
}
