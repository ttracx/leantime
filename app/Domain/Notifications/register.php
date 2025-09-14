<?php

use Safe4Work\Core\Events\EventDispatcher;
use Safe4Work\Domain\Notifications\Listeners\NotifyProjectUsers;

EventDispatcher::add_event_listener('leantime.domain.projects.services.projects.notifyProjectUsers.notifyProjectUsers', NotifyProjectUsers::class);
