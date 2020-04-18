<?php

namespace EmailCollector\Services\Outlook;

use EmailCollector\Services\BaseController;
use GuzzleHttp\Psr7\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Noodlehaus\Config;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

session_start();

class OutlookService extends BaseController
{
    const AUTHORITY_URL = 'https://login.microsoftonline.com/consumers';
    const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';
    const TOKEN_ENDPOINT = '/oauth2/v2.0/token';
    const SCOPES = 'openid offline_access User.Read Mail.Read';

    //minimun that is retrieved
    private $count;

    private $pagination;

    public function auth(Request $request, Response $response)
    {

        //We store user name, id, and tokens in session variables
//        if (session_status() == PHP_SESSION_NONE) {
//            session_start();
//        }

        $config = Config::load(realpath('outlook.json'));

        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $redirectUrl = $config['redirect_url'];

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUrl,
            'urlAuthorize' => OutlookService::AUTHORITY_URL . OutlookService::AUTHORIZE_ENDPOINT,
            'urlAccessToken' => OutlookService::AUTHORITY_URL . OutlookService::TOKEN_ENDPOINT,
            'urlResourceOwnerDetails' => '',
            'scopes' => OutlookService::SCOPES,
        ]);


        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['code'])) {
            $authorizationUrl = $provider->getAuthorizationUrl();

            // The OAuth library automaticaly generates a state value that we can
            // validate later. We just save it for now.
            $_SESSION['state'] = $provider->getState();
            return $response->withHeader('Location', $authorizationUrl);

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {

            // Validate the OAuth state parameter
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {

                unset($_SESSION['state']);
                throw new \Exception('State value does not match the one initially sent');
            }


            // With the authorization code, we can retrieve access tokens and other data.
            try {

                //Note:: if token expires catch exception and set refresh token into request. (refresh_token)
                // Get an access token using the authorization code grant
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code'],
                ]);


                $_SESSION['access_token_outlook'] = $accessToken->getToken();

                // The id token is a JWT token that contains information about the user
                // It's a base64 coded string that has a header, payload and signature
                //$idToken = $accessToken->getValues()['id_token'];

                return $response->withHeader('Location', '/outlook_test');

            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                throw new \Exception('Something went wrong, couldn\'t get tokens: ' . $e->getMessage());
            }
        }
    }

    public function example(Request $request, Response $response)
    {
        $emails = [];
        $graph = new Graph();
        $graph->setAccessToken($_SESSION['access_token_outlook']);
        $param = 'virginmedia';

        $emailResponse = $graph->createRequest("GET", "/me/messages?\$search={$param}")
            ->execute();

        while (true) {

            $mailList = $emailResponse->getResponseAsObject(Model\Message::class);

            foreach ($mailList as $email) {
                $emails[$email->getInternetMessageId()][] = $email->getReceivedDateTime()->format('c') ?? null;
                $emails[$email->getInternetMessageId()][] = $email->getSubject();
                $emails[$email->getInternetMessageId()][] = $email->getImportance()->value();
                $emails[$email->getInternetMessageId()][] = $email->getSender()->getEmailAddress()->getAddress();
            }

            $data = $emails;


            $this->pagination = $emailResponse->getNextLink();

            if ($this->pagination == null) {
                return $this->json(['data' => $data], $response);
            }

            $this->count = 1;

            while ($this->count > 0) {
                $emailResponse = $graph->createRequest("GET", $this->pagination)
                    ->execute();

                $mailList = $emailResponse->getResponseAsObject(Model\Message::class);

                foreach ($mailList as $email) {
                    $emails[$email->getInternetMessageId()][] = $email->getReceivedDateTime()->format('c') ?? null;
                    $emails[$email->getInternetMessageId()][] = $email->getSubject();
                    $emails[$email->getInternetMessageId()][] = $email->getImportance()->value();
                    $emails[$email->getInternetMessageId()][] = $email->getSender()->getEmailAddress()->getAddress();
                }

                $data = $emails;

                $this->pagination = $emailResponse->getNextLink();

                if ($this->pagination == null) {
                    return $this->json(['meta' => ['total' => count($data)], 'data' => $data], $response);
                }

                $this->count++;
            }
        }
    }
}
