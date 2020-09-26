<?php

declare(strict_types=1);

namespace EmailCollector\Services\EmailCollectionService;


class Email
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
     * @return Email
     */
    public function withIsBoolean(bool $isBoolean): Email
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
     * @return Email
     */
    public function withLabelIds(array $labelIds): Email
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
     * @return Email
     */
    public function withMaxResults(int $maxResults): Email
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
     * @return Email
     */
    public function withEmail(string $email): Email
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
     * @return Email
     */
    public function withUserId($userId): Email
    {
        $this->userId = $userId;
        return $this;
    }
}
