<?php

namespace Safe4Work\Domain\Errors\Controllers;

use Safe4Work\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Error404 extends Controller
{
    /**
     * @throws \Exception
     */
    public function run(): Response
    {
        return $this->tpl->display('errors.error404', layout: 'error', responseCode: 404);
    }
}
