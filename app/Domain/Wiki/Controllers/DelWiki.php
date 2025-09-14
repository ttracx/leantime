<?php

namespace Safe4Work\Domain\Wiki\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Wiki\Repositories\Wiki as WikiRepository;

class DelWiki extends Controller
{
    private WikiRepository $wikiRepo;

    /**
     * init - init
     */
    public function init(WikiRepository $wikiRepo)
    {
        $this->wikiRepo = $wikiRepo;
    }

    /**
     * run - display template and edit data
     */
    public function run()
    {

        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        if (isset($_GET['id'])) {
            $id = (int) ($_GET['id']);
        }

        if (isset($_POST['del']) && isset($id)) {
            $this->wikiRepo->delWiki($id);

            $this->tpl->setNotification($this->language->__('notification.wiki_deleted'), 'success', 'wiki_deleted');

            session()->forget('lastArticle');
            session()->forget('currentWiki');

            return Frontcontroller::redirect(BASE_URL.'/wiki/show');
        }

        return $this->tpl->displayPartial('wiki.delWiki');
    }
}
