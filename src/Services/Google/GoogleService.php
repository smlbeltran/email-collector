<?php

namespace EmailCollector\Services\Google;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

session_start();

/**
 * Class GoogleService
 * @package EmailCollector\Services\Google
 *
 * This class allows you to establish a connection with Google
 * and return an instance of the client.
 */
class GoogleService
{
    private $client;

    public function __construct()
    {
        $this->client = new \Google_Client();
    }

    public function connect()
    {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $this->withGoogleClientConfiguration();
            return $this->client;
        }

        throw new \Exception('Google client not available');
    }

    public function auth(Request $request, Response $response)
    {
        $this->withGoogleClientConfiguration();

        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            //send us to google consent page
            return $response->withHeader('Location', $auth_url);
        } else {
            //swap code for access tokens
            $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/emails';
            return $response->withHeader('Location', $redirect_uri);
        }
    }

    private function withGoogleClientConfiguration()
    {
        $this->client->setAuthConfig(realpath('credentials.json')); //information to identify my application

        //parameter that we want the user to give us access!!!

        $this->client->addScope(\Google_Service_Gmail::GMAIL_READONLY);
        $this->client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/auth'); // handle the response from google

        // offline access will give you both an access and refresh token so that
        // your app can refresh the access token without user interaction.
        $this->client->setAccessType('offline');

        // Using "consent" ensures that your application always receives a refresh token.
        // If you are not using offline access, you can omit this.
        $this->client->setApprovalPrompt("auto");
        $this->client->setIncludeGrantedScopes(true);
    }

}
