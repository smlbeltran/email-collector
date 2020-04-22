<?php

namespace EmailCollector\Services\Outlook;

use EmailCollector\Service\Outlook\Outlook;
use EmailCollector\Services\EmailCollectionService\EmailCollectionInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class OutlookService implements EmailCollectionInterface
{
    private $count;

    private $pagination;

    private function mapToOutlookModel($payload)
    {
        return (new Outlook())
            ->withEmail($payload->email);
    }

    public function collect($payload)
    {
        $emails = [];
        $graph = new Graph();
        $graph->setAccessToken($_SESSION['access_token_outlook']);

        $param = $this->mapToOutlookModel($payload);

        $emailResponse = $graph->createRequest("GET", "/me/messages?\$search={$param->getEmail()}")
            ->execute();

        while (true) {

            $mailList = $emailResponse->getResponseAsObject(Model\Message::class);

            foreach ($mailList as $email) {
                $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getReceivedDateTime()->format('c') ?? null;
                $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getSubject();
                $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getImportance()->value();
                $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getWebLink();
            }

            $data = $emails;

            $this->pagination = $emailResponse->getNextLink();

            if ($this->pagination == null) {
                return $data;
            }

            $this->count = 1;

            while ($this->count > 0) {
                $emailResponse = $graph->createRequest("GET", $this->pagination)
                    ->execute();

                $mailList = $emailResponse->getResponseAsObject(Model\Message::class);

                foreach ($mailList as $email) {
                    $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getReceivedDateTime()->format('c') ?? null;
                    $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getSubject();
                    $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getImportance()->value();
                    $emails[$email->getSender()->getEmailAddress()->getAddress()][] = $email->getWebLink();
                }

                $data = $emails;

                $this->pagination = $emailResponse->getNextLink();

                if ($this->pagination == null) {
                    return $data;
                }

                $this->count++;
            }
        }
    }
}
