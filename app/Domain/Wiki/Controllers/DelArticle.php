<?php

namespace Safe4Work\Domain\Wiki\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Wiki\Repositories\Wiki as WikiRepository;

class DelArticle extends Controller
{
    private WikiRepository $wikiRepo;

    /**
     * init - initialize private variables
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
            $this->wikiRepo->delArticle($id);

            $this->tpl->setNotification($this->language->__('notification.article_deleted'), 'success', 'article_deleted');

            session()->forget('lastArticle');
            session()->forget('currentWiki');

            return Frontcontroller::redirect(BASE_URL.'/wiki/show');
        }

        return $this->tpl->displayPartial('wiki.delArticle');
    }
}
