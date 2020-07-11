<?php

namespace EmailCollector\Providers;

use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\Google;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class GoogleOAuthProvider
 * @package EmailCollector\Providers
 */
class GoogleOAuthProvider implements OAuthConnectInterface
{
    public function authenticate(Request $request, Response $response)
    {
        if (!isset($_SESSION['access_token'])) {

            $config = Config::load(realpath('google_credentials.json'));

            $clientId = $config['web.client_id'];
            $clientSecret = $config['web.client_secret'];
            $redirectUrl = $config['web.redirect_uris'];

            $provider = new Google([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUrl[0],
                'accessType' => 'offline',
            ]);

            if (!empty($_GET['error'])) {

                exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

            } elseif (empty($_GET['code'])) {

                $authUrl = $provider->getAuthorizationUrl([
                    'scope' => [
                        \Google_Service_Gmail::GMAIL_READONLY
                    ],
                    'prompt' => 'consent',
                ]);

                $_SESSION['oauth2state'] = $provider->getState();

                header('Location:' . $authUrl);
                exit;

            } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
                exit('Invalid state');

            } else {

                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $refreshToken = $token->getRefreshToken();

                if ($refreshToken != null) {

                    $file = json_decode(file_get_contents('./credentials.json'));

                    $file->web->refresh_token = $refreshToken;

                    file_put_contents('./google_credentials.json', json_encode($file));
                } else {
                    $refreshToken = $config['web.refresh_token'];
                }

                $grant = new RefreshToken();
                $token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);

                unset($_GET['code']);
                unset($_GET['state']);

                $_SESSION['access_token'] = $token->getToken();
            }
        }
    }

}
