<?php

namespace App\Providers;

use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\Google;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class GoogleOAuthProvider
 * @package App\Providers
 */
class GoogleOAuthProvider implements OAuthConnectInterface
{
    private const CREDENTIALS_PATH = __DIR__ . '/../../credentials.json';
    /**
     * @var $client object
     */
    private $client;

    /**
     * @var $conf object
     */
    private $conf;

    public function __construct()
    {
        $this->conf = Config::load(self::CREDENTIALS_PATH);

        $this->client = new Google(
            [
                'clientId' => $this->conf['web.client_id'],
                'clientSecret' => $this->conf['web.client_secret'],
                'redirectUri' => $this->conf['web.redirect_uris'][0],
                'accessType' => 'offline',
            ]
        );
    }

    public function authenticate(Request $request, Response $response)
    {
        try {
            $this->displayConsentForm();
            $this->userTokenRequest();
        } catch (\Exception $e) {
            // get exception and do something with it and leave the program
        }
    }

    /**
     * displayConsentForm is a user/google interaction
     * where authentication and authorization process are ran if access tokens are not set.
     */
    private function displayConsentForm()
    {
        if (!empty($_GET['error'])) {
            throw new Exception('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
        }

        if (empty($_GET['code'])) {
            $authUrl = $this->client->getAuthorizationUrl(
                [
                    'scope' => [
                        \Google_Service_Gmail::GMAIL_READONLY
                    ],
                    'prompt' => 'consent',
                ]
            );

            $_SESSION['oauth2state'] = $this->client->getState();

            //redirect to Google consent form
            header('Location:' . $authUrl);
            exit;
        }

        if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
    }

    /**
     * userTokenRequest retrieves an access token allowing the user to communicate
     * with Google email APIs.
     */
    private function userTokenRequest()
    {
        $token = $this->client->getAccessToken(
            'authorization_code',
            [
                'code' => $_GET['code']
            ]
        );

        $refreshToken = $token->getRefreshToken();

        if ($refreshToken != null) {
            $file = json_decode(file_get_contents(slef::CREDENTIALS_PATH), true);

            $file['web']["refresh_token"] = $refreshToken;

            file_put_contents(self::CREDENTIALS_PATH, json_encode($file));
        } else {
            $refreshToken = $this->conf['web.refresh_token'];
        }

        $this->accessToken($refreshToken);

        unset($_GET['code']);
        unset($_GET['state']);
    }

    public function accessToken($refreshToken = null)
    {
        if ($refreshToken == null) {
            $refreshToken = $this->conf['web']['refresh_token'];
        }
        $grant = new RefreshToken();
        $token = $this->client->getAccessToken($grant, ['refresh_token' => $refreshToken]);
        $_SESSION['access_token'] = $token->getToken();
    }
}
