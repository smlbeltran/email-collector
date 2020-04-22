<?php


namespace EmailCollector\Providers;

use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;

interface OAuthConnectInterface
{
    public function authenticate(Request $request, Response $response);
}
