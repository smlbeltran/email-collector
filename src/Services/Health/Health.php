<?php

namespace EmailCollector\Services\Health;

use EmailCollector\Services\BaseController;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Health extends BaseController
{
    public function getHealth(Request $request, Response $response)
    {
        $time = (new \DateTime())->format('Y-m-d H:i:s');
        $payload = [
            "response" => "ok",
            "time" => $time
        ];
        return $this->json($payload, $response);
    }
}
