<?php

/**
 * Controller / Delete Canvas
 */

namespace Safe4Work\Domain\Notifications\Controllers;

use Safe4Work\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetLatestGrowl extends Controller
{
    public function init(

    ): void {}

    public function get()
    {

        $jsonEncoded = false;

        if (session('notification') != '') {
            $notificationArray = [
                'notification' => session('notification') ?? '',
                'type' => session('notificationType') ?? '',
                'eventId' => session('eventId') ?? '',
            ];

            session(['notification' => '']);
            session(['notificationType' => '']);
            session(['eventId' => '']);

            $jsonEncoded = json_encode($notificationArray);
        }

        return new JsonResponse($jsonEncoded);

    }
}
