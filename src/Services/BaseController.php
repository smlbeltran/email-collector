<?php

namespace EmailCollector\Services;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;

use function GuzzleHttp\Psr7\stream_for;


abstract class BaseController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * AbstractRoute constructor.
     *
     * All routing groups have access to the container.
     *
     * @param ContainerInterface $container
     */
    final public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * Return the Slim response as JSON
     *
     * @param $json
     * @param Response $response
     * @param int $status
     * @return Response
     * @internal param mixed $data The JSON data to render
     */
    protected function json($json, Response $response, $status = 200)
    {
        $stream = stream_for(is_string($json) ? $json : json_encode($json));

        $response = $response->withBody($stream)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withStatus($status);


        return $response;
    }

}
