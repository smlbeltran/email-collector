<?php


namespace EmailCollector\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use GuzzleHttp\Psr7\Response;

class MiddlewareRedirect
{

    // this is to verify the JWT token before moving the user to the endpoints
    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        if (!isset($_SESSION['access_token']) && !isset($_SESSION['access_token_outlook'])) {

            throw new \Exception('email services not authenticated');
        }


        $response = $handler->handle($request);

        return $response;

    }
}
