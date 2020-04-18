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
        // get response object
        $response = $handler->handle($request);
        // check for return info

        // redirect to right endpoint for auth

        if ($response->hasHeader('Location')) {
            $response->getHeader('Location');
//             header('Location: ' . filter_var($request->getHeader('Location'), FILTER_SANITIZE_URL));
        }


        return $response;
    }
}
