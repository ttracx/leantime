<?php

/**
 * Controller / Edit Canvas Item
 */

namespace Safe4Work\Domain\Goalcanvas\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Comments\Repositories\Comments as CommentRepository;
use Safe4Work\Domain\Goalcanvas\Repositories\Goalcanvas as GoalcanvaRepository;
use Safe4Work\Domain\Goalcanvas\Services\Goalcanvas as GoalcanvaService;
use Safe4Work\Domain\Projects\Services\Projects as ProjectService;
use Safe4Work\Domain\Tickets\Services\Tickets as TicketService;
use Symfony\Component\HttpFoundation\Response;

class BigRock extends \Leantime\Domain\Canvas\Controllers\EditCanvasItem
{
    protected const CANVAS_NAME = 'goal';

    private GoalcanvaRepository $canvasRepo;

    private CommentRepository $commentsRepo;

    private TicketService $ticketService;

    private ProjectService $projectService;

    private GoalcanvaService $goalService;

    public function init(
        GoalcanvaRepository $canvasRepo,
        CommentRepository $commentsRepo,
        TicketService $ticketService,
        ProjectService $projectService,
        GoalcanvaService $goalService
    ): void {
        $this->canvasRepo = $canvasRepo;
        $this->commentsRepo = $commentsRepo;
        $this->ticketService = $ticketService;
        $this->projectService = $projectService;
        $this->goalService = $goalService;
    }

    /**
     * @throws \Exception
     */
    public function get($params): Response
    {
        if (isset($params['id'])) {

            $bigrock = $this->goalService->getSingleCanvas($params['id']);

        } else {

            $bigrock = ['id' => '', 'title' => '', 'prpojectId' => '', 'author' => ''];
        }

        $this->tpl->assign('bigRock', $bigrock);

        return $this->tpl->displayPartial('goalcanvas.bigRockDialog');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {
        $bigrock = ['id' => '', 'title' => '', 'projectId' => '', 'author' => ''];

        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            // Update
            $bigrock['id'] = $id;
            $bigrock['title'] = $params['title'];
            $this->goalService->updateGoalboard($bigrock);
            $this->tpl->setNotification('notification.goalboard_updated_successfully', 'success', 'goalcanvas_updated');

            return Frontcontroller::redirect(BASE_URL.'/goalcanvas/bigRock/'.$id);

        } else {
            // New
            $bigrock['title'] = $params['title'];
            $bigrock['projectId'] = session('currentProject');
            $bigrock['author'] = session('userdata.id');

            $id = $this->goalService->createGoalboard($bigrock);

            if ($id) {
                $this->tpl->setNotification('notification.goalboard_created_successfully', 'success', 'wiki_created');

                return Frontcontroller::redirect(BASE_URL.'/goalcanvas/bigRock/'.$id.'?closeModal=1');
            }

            return Frontcontroller::redirect(BASE_URL.'/goalcanvas/bigRock/'.$id.'');
        }
    }
}
