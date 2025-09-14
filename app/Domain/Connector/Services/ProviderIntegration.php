<?php

namespace Safe4Work\Domain\Connector\Services;

use Safe4Work\Domain\Connector\Models\Entity;

interface ProviderIntegration
{
    public function connect(): mixed;

    public function sync(Entity $entity): mixed;

    public function getFields(): mixed;

    public function getEntities(): mixed;

    public function getValues(Entity $entity): mixed;
}
