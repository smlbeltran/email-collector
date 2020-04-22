<?php

namespace EmailCollector\Service\Gmail;

use EmailCollector\Services\EmailCollectionService\EmailCollectionInterface;

/**
 * Class GmailService
 * @package EmailCollector\Service\Gmail
 *
 * This class is responsible for interacting with Gmail APIs to interact with
 * different CRUD operation across different messages we choose to work with
 */
class GmailService implements EmailCollectionInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new \Google_Client();

    }

    public function connect()
    {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

            return $this->client->setAccessToken($_SESSION['access_token']);
        }

        throw new \Exception('Google client not available');
    }

    private function mapToGmailModel($payload)
    {
        return (new Gmail())
            ->withUserId('me')
            ->withLabelIds($payload->labels)
            ->withMaxResults($payload->max_results)
            ->withEmail($payload->email)
            ->withIsBoolean($payload->include_spam_trash);
    }

    /**
     *
     * fetch all emails from Gmail
     * @param $payload
     * @return array
     * @throws \Exception
     */
    public function collect($payload)
    {
        $model = $this->mapToGmailModel($payload);

        $this->connect();

        $mailService = new \Google_Service_Gmail($this->client);

        $mails = $mailService->users_messages->listUsersMessages($model->getUserId(), [
            'q' => $model->getEmail(),
            'maxResults' => (int)$model->getMaxResults(),
            'labelIds' => $model->getLabelIds(),
            'includeSpamTrash' => $model->isBoolean(),
        ]);

        $msgList = [];
        foreach ($mails as $k => $message) {
            $msg = $mailService->users_messages->get('me', $message->id);

            $msgList[$msg->getId()] = [
                'title' => $msg->getSnippet(),
                'labels' => $msg->getLabelIds()
            ];

        }
        return $msgList;
    }
}
