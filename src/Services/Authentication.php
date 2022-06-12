<?php


namespace App\Services;

use App\Providers\GoogleOAuthProvider;
use App\Providers\OutlookOAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Authentication extends BaseController
{
    public function googleAuth(Request $request, Response $response)
    {
        $authProvider = new GoogleOAuthProvider();
        $authProvider->authenticate($request, $response);

        return $this->json(['google_user_authenticated' => true], $response);

    }

    public function outlookAuth(Request $request, Response $response)
    {
        $authProvider = new OutlookOAuthProvider();
        $authProvider->authenticate($request, $response);
        return $this->json(['outlook_user_authenticated' => true], $response);
    }

}
