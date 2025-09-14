<?php

namespace Safe4Work\Domain\Wiki\Controllers;

use Safe4Work\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Templates extends Controller
{
    public function init(): void {}

    /**
     * @throws \Exception
     */
    public function get($params): Response
    {
        return $this->tpl->displayPartial('wiki.templates');
    }
}
