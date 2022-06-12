<?php

namespace App\Services\EmailCollectionService;

interface EmailCollectionInterface
{
    public function getEmails($nextPageLink);
}
