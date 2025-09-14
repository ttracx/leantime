<?php

namespace Safe4Work\Domain\Queue;

use Illuminate\Console\Scheduling\Schedule;
use Safe4Work\Core\Events\EventDispatcher;
use Safe4Work\Domain\Queue\Workers\Workers;

EventDispatcher::add_event_listener('leantime.core.console.consolekernel.schedule.cron', function ($params) {

    /** @var Schedule $scheduler */
    if (get_class($scheduler = $params['schedule']) !== Schedule::class) {
        return;
    }

    $scheduler
        ->call(fn () => app()->make(Services\Queue::class)->processQueue(Workers::EMAILS))
        ->name('queue:emails')
        ->everyMinute();

    $scheduler
        ->call(fn () => app()->make(Services\Queue::class)->processQueue(Workers::HTTPREQUESTS))
        ->name('queue:httprequests')
        ->everyFiveMinutes();

    $scheduler
        ->call(fn () => app()->make(Services\Queue::class)->processQueue(Workers::DEFAULT))
        ->name('queue:default')
        ->everyFiveMinutes();
});
