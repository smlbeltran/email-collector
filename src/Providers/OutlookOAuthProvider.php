<?php


namespace EmailCollector\Providers;

use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Grant\RefreshToken;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OutlookOAuthProvider
 * @package EmailCollector\Providers
 */
class OutlookOAuthProvider implements OAuthConnectInterface
{
    const AUTHORITY_URL = 'https://login.microsoftonline.com/consumers';
    const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';
    const TOKEN_ENDPOINT = '/oauth2/v2.0/token';
    const SCOPES = 'openid offline_access User.Read Mail.Read';

    /**
     * @var object
     */
    private $conf;

    /**
     * @var object
     */
    private $client;

    public function __construct()
    {
        $this->conf = Config::load(realpath('outlook.json.json'));

        $clientId = $this->conf['client_id'];
        $clientSecret = $this->conf['client_secret'];
        $redirectUrl = $this->conf['redirect_url'];

        $this->client = new \League\OAuth2\Client\Provider\GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUrl,
                'urlAuthorize' => OutlookOAuthProvider::AUTHORITY_URL . OutlookOAuthProvider::AUTHORIZE_ENDPOINT,
                'urlAccessToken' => OutlookOAuthProvider::AUTHORITY_URL . OutlookOAuthProvider::TOKEN_ENDPOINT,
                'urlResourceOwnerDetails' => '',
                'scopes' => OutlookOAuthProvider::SCOPES,
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['code'])) {
            $authorizationUrl = $this->client->getAuthorizationUrl();

            $_SESSION['state'] = $this->client->getState();

            header('Location:' . $authorizationUrl);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {
                unset($_SESSION['state']);
                throw new \Exception('State value does not match the one initially sent');
            }
        }
    }

    /**
     * userTokenRequest retrieves an access token allowing the user to communicate
     * with Google email APIs.
     */
    private function userTokenRequest()
    {
        try {
            $accessToken = $this->client->getAccessToken(
                'authorization_code',
                [
                    'code' => $_GET['code'],
                ]
            );

            $refreshToken = $accessToken->getRefreshToken();

            if ($refreshToken != null) {
                $file = json_decode(file_get_contents('./outlook.json'), true);

                $file['refresh_token'] = $refreshToken;

                file_put_contents('./outlook.json', json_encode($file));
            } else {
                $refreshToken = $this->conf['refresh_token'];
            }

            $grant = new RefreshToken();
            $token = $this->client->getAccessToken($grant, ['refresh_token' => $refreshToken]);

            $_SESSION['access_token_outlook'] = $token->getToken();
        } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            throw new \Exception('Something went wrong, couldn\'t get tokens: ' . $e->getMessage());
        }
    }

    public function accessToken($refreshToken = null)
    {
        if ($refreshToken == null) {
            $refreshToken = $this->conf['refresh_token'];
        }
        $grant = new RefreshToken();
        $token = $this->client->getAccessToken($grant, ['refresh_token' => $refreshToken]);
        $_SESSION['access_token'] = $token->getToken();
    }
}
