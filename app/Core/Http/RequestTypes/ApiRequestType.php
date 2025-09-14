<?php

namespace Safe4Work\Core\Http\RequestTypes;

use Safe4Work\Core\Http\ApiRequest;
use Safe4Work\Core\Http\IncomingRequest;

class ApiRequestType implements RequestTypeInterface
{
    public function matches(IncomingRequest $request): bool
    {
        $requestUri = strtolower($request->getRequestUri());

        return
            $request->headers->has('x-api-key')
            || $request->bearerToken()
            || $request->isApiRequest();
    }

    public function getPriority(): int
    {
        return 300; // Higher priority than HTMX
    }

    public function getRequestClass(): string
    {
        return ApiRequest::class;
    }
}
