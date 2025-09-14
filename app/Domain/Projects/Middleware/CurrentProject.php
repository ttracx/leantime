<?php

namespace Safe4Work\Domain\Projects\Middleware;

use Closure;
use Safe4Work\Core\Http\HtmxRequest;
use Safe4Work\Core\Http\IncomingRequest;
use Safe4Work\Domain\Auth\Services\Auth as AuthService;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Symfony\Component\HttpFoundation\Response;

class CurrentProject
{
    /**
     * Set the current project
     *
     * @param  \Closure(IncomingRequest): Response  $next
     **/
    public function handle(IncomingRequest $request, Closure $next): Response
    {

        if (app()->make(AuthService::class)->loggedIn()) {

            $actionPath = $request->getModuleName();

            // Only change/set project if the request is not htmx, api or cron
            if (! ($request instanceof HtmxRequest) && $actionPath != 'api' && $actionPath != 'cron') {
                app()->make(ProjectService::class)->setCurrentProject();
            }
        }

        return $next($request);
    }
}
