<?php

namespace Safe4Work\Core\Http\RequestTypes;

use Safe4Work\Core\Http\IncomingRequest;

interface RequestTypeInterface
{
    /**
     * Check if the request matches this type
     */
    public function matches(IncomingRequest $request): bool;

    /**
     * Get the priority of this request type
     * Higher numbers mean higher priority
     */
    public function getPriority(): int;

    /**
     * Get the request class to instantiate
     */
    public function getRequestClass(): string;
}
