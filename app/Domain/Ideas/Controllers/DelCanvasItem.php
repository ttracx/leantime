<?php

namespace Safe4Work\Domain\Ideas\Controllers;

use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Auth\Models\Roles;
use Safe4Work\Domain\Auth\Services\Auth;
use Safe4Work\Domain\Ideas\Repositories\Ideas as IdeaRepository;

class DelCanvasItem extends Controller
{
    private IdeaRepository $ideasRepo;

    /**
     * init - initialize private variables
     */
    public function init(IdeaRepository $ideasRepo)
    {
        $this->ideasRepo = $ideasRepo;
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
            $this->ideasRepo->delCanvasItem($id);

            $this->tpl->setNotification($this->language->__('notification.idea_board_item_deleted'), 'success', 'ideaitem_deleted');

            return Frontcontroller::redirect(BASE_URL.'/ideas/showBoards');
        }

        return $this->tpl->displayPartial('ideas.delCanvasItem');
    }
}
