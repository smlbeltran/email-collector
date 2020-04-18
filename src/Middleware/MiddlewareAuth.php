<?php

namespace EmailCollector\Middleware;

use Firebase\JWT\JWT;
use Noodlehaus\Config;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareAuth
{
    // this is to verify the JWT token before moving the user to the endpoints
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (!$request->hasHeader('Authorization')) {
            throw new \Exception('token required to continue with operation');
        }

        $token = $request->getHeader('Authorization');

        $conf = (Config::load(realpath('config.json')))['apiKey'];

        try {
            JWT::decode($token[0], $conf, ['HS512']);

        }catch (\Exception $e) {
            throw new \Exception('invalid token: ' . $e->getMessage());
            // if token is expired revoke it and create new one???
        }

        $response = $handler->handle($request);

        return $response;
    }
}
