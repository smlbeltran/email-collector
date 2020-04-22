<?php

declare(strict_types=1);

namespace EmailCollector\Service\Outlook;


class Outlook
{
    private $labelIds = [];

    private $maxResults;

    private $email;

    private $userId;

    private $isBoolean;

    /**
     * @return mixed
     */
    public function isBoolean(): bool
    {
        return $this->isBoolean;
    }

    /**
     * @param mixed $isBoolean
     * @return Outlook
     */
    public function withIsBoolean(bool $isBoolean): Outlook
    {
        $this->isBoolean = $isBoolean;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabelIds()
    {
        return $this->labelIds;
    }

    /**
     * @param array $labelIds
     * @return Outlook
     */
    public function withLabelIds(array $labelIds): Outlook
    {
        $this->labelIds = $labelIds;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @param mixed $maxResults
     * @return Outlook
     */
    public function withMaxResults(int $maxResults): Outlook
    {

        $this->maxResults = $maxResults;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Outlook
     */
    public function withEmail(string $email): Outlook
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return Outlook
     */
    public function withUserId($userId): Outlook
    {
        $this->userId = $userId;
        return $this;
    }
}
