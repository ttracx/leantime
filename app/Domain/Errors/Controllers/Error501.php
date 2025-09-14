<?php

namespace Safe4Work\Domain\Errors\Controllers;

use Safe4Work\Core\Controller\Controller;

class Error501 extends Controller
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->tpl->display(
            template: 'errors.error501',
            layout: 'error',
            responseCode: 501);
    }
}
