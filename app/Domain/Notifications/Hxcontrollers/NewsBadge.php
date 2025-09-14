<?php

namespace Safe4Work\Domain\Notifications\Hxcontrollers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Timesheets\Services\Timesheets;

class NewsBadge extends HtmxController
{
    protected static string $view = 'notifications::partials.newsBadge';

    private \Leantime\Domain\Notifications\Services\News $newsService;

    /**
     * Controller constructor
     *
     * @param  Timesheets  $timesheetService
     * @param  Menu  $menuService
     * @param  \Leantime\Domain\Menu\Repositories\Menu  $menuRepo
     */
    public function init(\Leantime\Domain\Notifications\Services\News $newsService): void
    {
        $this->newsService = $newsService;

    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     */
    public function get()
    {

        try {
            $hasNews = $this->newsService->hasNews(session('userdata.id'));
        } catch (\Exception $e) {
            report($e);
            $hasNews = false;
        }

        $this->tpl->assign('hasNews', $hasNews);
    }
}
