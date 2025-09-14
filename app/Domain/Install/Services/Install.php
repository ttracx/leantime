<?php

namespace Safe4Work\Domain\Install\Services;

use Safe4Work\Core\Configuration\AppSettings;

class Install
{
    public function __construct(protected AppSettings $appSettings) {}

    /**
     * currentVersion - gets the currently installed leantime version
     *
     * @api
     */
    public function currentVersion(): string
    {
        return $this->appSettings->appVersion;
    }
}
