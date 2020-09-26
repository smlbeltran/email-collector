<?php

namespace EmailCollector\Services\User;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiKey extends BaseController
{

    public function create(Request $request, Response $response)
    {
        $payload = json_decode($request->getBody()->getContents());

        // we need to check database to see if the user exists
        $config = $this->container->get('Config');

        $secretKey = base64_encode($config['apikey']);
        /** @var \EmailCollector\Services\User\UserDatabaseInterface $user */
        $user = $this->container->get('UserDatabaseInterface');
        $user = $user->getOne($payload);

        $payload = array(
            "iss" => "email-collector",
            "iat" => time(),
            "nbf" => time() + 10,
            "exp" => time() + 3600,
            "data" => $user
        );

        $jwt = JWT::encode($payload, $config['apiKey'], 'HS512');

        return $this->json(['jwt' => $jwt], $response, 200);
    }
}
