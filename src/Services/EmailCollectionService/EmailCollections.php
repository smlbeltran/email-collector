<?php

namespace EmailCollector\Services\EmailCollectionService;

use EmailCollector\Service\Gmail\GmailService;
use EmailCollector\Services\BaseController;
use EmailCollector\Services\Outlook\OutlookService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class EmailCollections extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $data = [];
        /** @var \EmailCollector\Helpers\JsonSchemaValidator $validator */
        $validator = $this->container->get('Validator');

        $payload = $validator->validate($request, 'Message/index');

        $emailServices = [
            new GmailService(),
            new OutlookService(),
        ];

        foreach ($emailServices as $service) {
            if ($service instanceof EmailCollectionInterface) {
                $data[get_class($service)] = $service->collect($payload);
            }
        }

        return $this->json(['data' => $data], $response);
    }
}
