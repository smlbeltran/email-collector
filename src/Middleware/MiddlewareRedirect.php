<?php

namespace App\Middleware;

use App\Providers\GoogleOAuthProvider;
use App\Providers\OutlookOAuthProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use GuzzleHttp\Psr7\Response;

class MiddlewareRedirect
{

    private const CREDENTIALS_PATH = __DIR__ . '/../../outlook.json';

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        /**
         * when your access_token expires you can use your refresh token to retrieve a new access_token as this never
         * expires if your refresh token has expired you may need to authenticate again.
         */
        if (!isset($_SESSION['access_token']) && !isset($_SESSION['access_token_outlook'])) {
            // we need to set access_tokens from the refresh token before so we don't bothering the user.
            throw new \Exception('email services not authenticated');
        }

        //check if token  expiry
        $guzzle = new Client();
        try {
            $guzzle->request(
                'GET',
                "https://oauth2.googleapis.com/tokeninfo",
                [
                    'query' => ['id_token' => $_SESSION['access_token']]
                ]
            );
        } catch (ClientException $e) {
            if ($e->getCode() == 400) {
                $client = new GoogleOAuthProvider();
                $client->accessToken();
            }
        }

        $conf = Config::load(self::CREDENTIALS_PATH);

        try {
            $guzzle->request(
                'POST',
                "https://login.microsoftonline.com/consumers/oauth2/v2.0/token",
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'form_params' => [
                        'client_id' => $conf['client_id'],
                        'grant_type' => 'refresh_token',
                        'scope' => 'openid offline_access User.Read Mail.Read',
                        'refresh_token' => $conf['refresh_token'],
                        'client_secret' => $conf['client_secret'],
                    ]
                ]
            );
        } catch (ClientException $e) {
            if ($e->getCode() == 400) {
                $client = new OutlookOAuthProvider();
                $client->accessToken();
            }
        }


        //add to access_token session
        $response = $handler->handle($request);

        return $response;
    }
}
