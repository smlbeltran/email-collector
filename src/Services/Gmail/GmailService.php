<?php

namespace EmailCollector\Service\Gmail;

use EmailCollector\Services\EmailCollectionService\EmailCollectionInterface;

/**
 * Class GmailService
 * @package EmailCollector\Service\Gmail
 *
 * This class is responsible for the interaction with the Gmail Api to fetch all emails
 * the user requests.
 */
class GmailService implements EmailCollectionInterface
{
    private $client;
    private $model;

    public function __construct($model)
    {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client = new \Google_Client();
            $this->client->setAccessToken($_SESSION['access_token']);
            $this->model = $model;
        }else{
            throw new \Exception('Google client not available');
        }
    }

    /**
     *
     * fetch all emails from Gmail
     * @param null $nextPageLink
     * @return array
     */
    public function getEmails($nextPageLink = null)
    {
        $googleServiceGmail = new \Google_Service_Gmail($this->client);

        $mails = $googleServiceGmail->users_messages->listUsersMessages($this->model->getUserId(), [
            'q' => 'from:'. $this->model->getEmail(),
            'maxResults' => (int)$this->model->getMaxResults(),
            'labelIds' => $this->model->getLabelIds(),
            'includeSpamTrash' => $this->model->isBoolean(),
        ]);

        $msgList = [];
        foreach ($mails as $k => $message) {
            $msg = $googleServiceGmail->users_messages->get('me', $message->id, ['format'=>'metadata','metadataHeaders'=> ['From'] ]);

            $msgList[] = [
                'title' => $msg->getSnippet(),
                'labels' => $msg->getLabelIds(),
                'from' => $msg->getPayload()->getHeaders()[0]->getValue(),
            ];

        }
        return $msgList;
    }
}
