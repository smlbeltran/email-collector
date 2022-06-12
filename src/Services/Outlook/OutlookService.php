<?php

namespace App\Services\Outlook;

use App\Service\Outlook\Outlook;
use App\Services\EmailCollectionService\EmailCollectionInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

/**
 * Class OutlookService
 * @package App\Services\Outlook
 * This class is responsible for the interaction with the Outlook Api (Microsoft Graph) to fetch all emails
 * the user requests.
 */
class OutlookService implements EmailCollectionInterface
{
    private $model;

    private $microsoftGraphApi;

    private $emails = [];

    public function __construct($model)
    {
        $this->model = $model;
        $this->microsoftGraphApi = new Graph();
        $this->microsoftGraphApi->setAccessToken($_SESSION['access_token_outlook']);
    }

    public function getEmails($nextPageLink = null)
    {
        $messages = null;
        $response = null;

        if ($nextPageLink == null) {
            //receives 25 items per page
            $response = $this->microsoftGraphApi->createRequest("GET", "/me/messages?\$search=" . "\"from:" . $this->model->getEmail() . '"')
                ->execute();

        } else {
            $response = $this->microsoftGraphApi->createRequest("GET", $nextPageLink)
                ->execute();
        }

        $messages = $response->getResponseAsObject(Model\Message::class);

        foreach ($messages as $message) {

            $this->emails[] = [
                'from' => $message->getSender()->getEmailAddress()->getAddress(),
                'date' => $message->getReceivedDateTime()->format('c') ?? null,
                'title' => $message->getSubject(),
                'label' => $message->getImportance()->value(),
                'link' => $message->getWebLink(),
            ];
        }

        if ($response->getNextLink() != null) {
            $this->getEmails($response->getNextLink());
        }

        return $this->emails;
    }

}
