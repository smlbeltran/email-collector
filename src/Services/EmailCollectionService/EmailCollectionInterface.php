<?php

namespace EmailCollector\Services\EmailCollectionService;

interface EmailCollectionInterface
{
    public function getEmails($nextPageLink);
}
