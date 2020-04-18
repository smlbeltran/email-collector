<?php

namespace EmailCollector\Service\Gmail;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use EmailCollector\Services\BaseController;

/**
 * Class GmailService
 * @package EmailCollector\Service\Gmail
 *
 * This class is responsible for interacting with Gmail APIs to interact with
 * different CRUD operation across different messages we choose to work with
 */
class GmailService extends BaseController
{

    private function mapToGmailModel($payload)
    {
        $gmail = (new Gmail())
            ->withUserId('me')
            ->withLabelIds($payload->labels ?? [])
            ->withMaxResults($payload->max_results)
            ->withQuery($payload->query ?? '')
            ->withIsBoolean($payload->include_spam_trash);

        return $gmail;
    }

    /**
     * we want to be able to connect to google client
     * and fetch all emails we request through our query
     * @param Request $request
     * @param Response $response
     * @return Response|void
     */
    public function index(Request $request, Response $response)
    {
        /** @var \EmailCollector\Helpers\JsonSchemaValidator $validator */
        $validator = $this->container->get('Validator');

        $payload = $validator->validate($request, 'Gmail/index');

        $model = $this->mapToGmailModel($payload);

        try {
            /** @var \EmailCollector\Services\Google\GoogleService $client */
            $client = $this->container->get('Google.Service');

            $client = $client->connect();

            $mailService = new \Google_Service_Gmail($client);

            $mails = $mailService->users_messages->listUsersMessages($model->getUserId(), [
                'q' => $model->getQuery(),
                'maxResults' => (int)$model->getMaxResults(),
                'labelIds' => $model->getLabelIds(),
                'includeSpamTrash' => $model->isBoolean(),
            ]);

        } catch (\Exception $e) {
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/auth';
            return $response->withHeader('Location', $redirect_uri);
        }

        $msgList = [];
        foreach ($mails as $k => $message) {
            $msg = $mailService->users_messages->get('me', $message->id);

            $msgList[$msg->getId()] = [
                'title' => $msg->getSnippet(),
                'labels' => $msg->getLabelIds()
            ];

        }

        return $this->json(['data' => $msgList], $response);
    }
}
