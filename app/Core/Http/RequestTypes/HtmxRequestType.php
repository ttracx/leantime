<?php

namespace Safe4Work\Core\Http\RequestTypes;

use Safe4Work\Core\Http\HtmxRequest;
use Safe4Work\Core\Http\IncomingRequest;

class HtmxRequestType implements RequestTypeInterface
{
    public function matches(IncomingRequest $request): bool
    {
        return $request->headers->has('HX-Request');
    }

    public function getPriority(): int
    {
        return 200;
    }

    public function getRequestClass(): string
    {
        return HtmxRequest::class;
    }
}
