<?php

namespace Safe4Work\Domain\Help\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Help\Services\Helper;

class Support extends Controller
{
    protected Helper $helpService;

    public function init(Helper $helpService)
    {
        $this->helpService = $helpService;

    }

    /**
     * get - handle get requests
     */
    public function get($params)
    {

        return $this->tpl->display('help.support');

    }
}
