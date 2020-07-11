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

    public function authenticate(Request $request, Response $response)
    {

        if (!isset($_SESSION['access_token_outlook'])) {

            $config = Config::load(realpath('outlook_credentials.json'));

            $clientId = $config['client_id'];
            $clientSecret = $config['client_secret'];
            $redirectUrl = $config['redirect_url'];

            $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUrl,
                'urlAuthorize' => OutlookOAuthProvider::AUTHORITY_URL . OutlookOAuthProvider::AUTHORIZE_ENDPOINT,
                'urlAccessToken' => OutlookOAuthProvider::AUTHORITY_URL . OutlookOAuthProvider::TOKEN_ENDPOINT,
                'urlResourceOwnerDetails' => '',
                'scopes' => OutlookOAuthProvider::SCOPES,
            ]);

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['code'])) {
                $authorizationUrl = $provider->getAuthorizationUrl();

                $_SESSION['state'] = $provider->getState();

                header('Location:' . $authorizationUrl);
                exit;

            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {

                if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {

                    unset($_SESSION['state']);
                    throw new \Exception('State value does not match the one initially sent');
                }

                try {

                    $accessToken = $provider->getAccessToken('authorization_code', [
                        'code' => $_GET['code'],
                    ]);


                    $refreshToken = $accessToken->getRefreshToken();

                    if ($refreshToken != null) {

                        $file = json_decode(file_get_contents('./outlook_credentials.json'));

                        $file->refresh_token = $refreshToken;

                        file_put_contents('./outlook.json', json_encode($file));
                    } else {
                        $refreshToken = $config['refresh_token'];
                    }

                    $grant = new RefreshToken();
                    $token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);


                    $_SESSION['access_token_outlook'] = $token->getToken();

                } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                    throw new \Exception('Something went wrong, couldn\'t get tokens: ' . $e->getMessage());
                }
            }
        }
    }
}
