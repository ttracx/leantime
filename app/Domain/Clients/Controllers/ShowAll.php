<?php

/**
 * showAll Class - Show all clients
 */

namespace Safe4Work\Domain\Clients\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Clients\Repositories\Clients as ClientRepository;

class ShowAll extends Controller
{
    private ClientRepository $clientRepo;

    /**
     * init - initialize private variables
     */
    public function init(ClientRepository $clientRepo)
    {

        $this->clientRepo = $clientRepo;
    }

    /**
     * run - display template and edit data
     */
    public function run()
    {

        Auth::authOrRedirect([Roles::$owner, Roles::$admin], true);

        if (session('userdata.role') == 'admin') {
            $this->tpl->assign('admin', true);
        }

        $this->tpl->assign('allClients', $this->clientRepo->getAll());

        return $this->tpl->display('clients.showAll');
    }
}
