<?php

require __DIR__ . '/vendor/autoload.php';

use Noodlehaus\Config;

$config = Config::load(realpath('outlook.json'));
$tenantId = $config['tenant_id'];
$clientId = $config['client_id'];
$clientSecret = $config['client_secret'];

$guzzle = new \GuzzleHttp\Client();
$url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
$token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        'grant_type' => 'client_credentials',
    ],

])->getBody()->getContents());

$accessToken = $token->access_token;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class UsageExample
{
    public function run($accessToken)
    {

        $graph = new Graph();

        $graph->setAccessToken($accessToken);




        $user = $graph->createRequest("GET", "/me")
            ->setReturnType(Model\User::class)
            ->execute();
//        $user = $graph->createRequest("GET", "/me")->execute();

        var_dump($user);
        die();



        echo "Hello, I am $user->getGivenName() ";
    }
}


(new UsageExample())->run($accessToken);

