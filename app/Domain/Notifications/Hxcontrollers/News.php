<?php

namespace Safe4Work\Domain\Notifications\Hxcontrollers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;
use Safe4Work\Core\Controller\HtmxController;
use Safe4Work\Domain\Menu\Services\Menu;
use Safe4Work\Domain\Timesheets\Services\Timesheets;

class News extends HtmxController
{
    protected static string $view = 'notifications::partials.latestNews';

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

        $news = false;
        try {
            $news = $this->newsService->getLatest(session('userdata.id'));

        } catch (\Exception $e) {
            Log::warning('Could not connect to news server');
        }

        if ($news === false) {
            $news = 'Could not connect to news server';
        }

        $this->tpl->assign('rss', $news);
    }
}
