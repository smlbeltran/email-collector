<?php

namespace App\Services\EmailCollectionService;

use App\Services\Gmail\GmailService;
use App\Services\BaseController;
use App\Services\Outlook\OutlookService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class EmailCollections
 * @package App\Services\EmailCollectionService
 * This class is responsible for gathering emails from Gmail & Outlook and return
 * the results to the user as a batch.
 */
class EmailCollections extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $data = [];
        /** @var \App\Helpers\JsonSchemaValidator $validator */
        $validator = $this->container->get('Validator');

        $payload = $validator->validate($request, 'Message/index');

        $emailModel = (new Email())->withUserId('me')
            ->withLabelIds($payload->labels)
            ->withMaxResults($payload->max_results)
            ->withEmail($payload->search)
            ->withIsBoolean($payload->include_spam_trash);;

        $emailServices = [
            new GmailService($emailModel),
            new OutlookService($emailModel),
        ];

        foreach ($emailServices as $service) {
            if ($service instanceof EmailCollectionInterface) {
                $data[get_class($service)] = $service->getEmails();
            }
        }


        return $this->json(['data' => $data], $response);
    }
}
