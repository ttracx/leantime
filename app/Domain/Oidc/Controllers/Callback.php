<?php

namespace Safe4Work\Domain\Oidc\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Exception\HttpResponseException;
use Safe4Work\Core\Controller\Controller;
use Safe4Work\Core\Controller\Frontcontroller;
use Safe4Work\Domain\Oidc\Services\Oidc as OidcService;
use Symfony\Component\HttpFoundation\Response;

class Callback extends Controller
{
    private OidcService $oidc;

    public function init(OidcService $oidc): void
    {
        $this->oidc = $oidc;
    }

    /**
     * @throws GuzzleException|HttpResponseException
     */
    public function get($params): Response
    {
        $code = $_GET['code'];
        $state = $_GET['state'];

        try {
            return $this->oidc->callback($code, $state);
        } catch (\Exception $e) {
            $this->tpl->setNotification($e->getMessage(), 'danger', 'oidc_error');

            return Frontcontroller::redirect(BASE_URL.'/auth/login');
        }
    }
}
