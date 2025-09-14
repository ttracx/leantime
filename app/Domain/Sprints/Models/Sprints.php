<?php

namespace Safe4Work\Domain\Sprints\Models;

class Sprints
{
    public $id;

    public $name;

    public $startDate;

    public $endDate;

    public $projectId;

    public $modified;

    public function __construct() {}
}
