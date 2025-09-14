<?php

namespace Safe4Work\Domain\Cron\Services;

use Safe4Work\Core\Configuration\Environment;
use Safe4Work\Core\Events\DispatchesEvents;
use Safe4Work\Domain\Audit\Repositories\Audit;
use Safe4Work\Domain\Queue\Services\Queue;
use Safe4Work\Domain\Reports\Services\Reports;

/**
 * @api
 */
class Cron
{
    use DispatchesEvents;

    private Audit $auditRepo;

    private Queue $queueSvc;

    private Environment $Environment;

    private Environment $environment;

    private Reports $reportService;

    private int $cronExecTimer = 60;

    public function __construct(Audit $auditRepo, Queue $queueSvc, Environment $environment, Reports $reportService)
    {
        $this->auditRepo = $auditRepo;
        $this->queueSvc = $queueSvc;
        $this->environment = $environment;
        $this->reportService = $reportService;
    }
}
