<?php

namespace Safe4Work\Domain\Connector\Repositories;

use Safe4Work\Core\Db\Repository;
use Safe4Work\Domain\Connector\Models\Integration;

class Integrations extends Repository
{
    public function __construct()
    {
        $this->entity = 'integration';
        $this->model = Integration::class;
    }
}
