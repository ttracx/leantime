<?php

namespace Safe4Work\Domain\Notifications\Models;

class Notification
{
    public int $id;

    public string $message;

    public string $subject;

    public int $projectId;

    public int $authorId;

    public bool|array $url;

    public mixed $entity;

    public string $module;
}
