<?php

declare(strict_types=1);

namespace EmailCollector\Service\Gmail;

/**
 * Class Gmail
 * @package EmailCollector\Service\Gmail
 * Initializer Setter & Getter for Gmail Service
 */
class Gmail
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
     * @return Gmail
     */
    public function withIsBoolean(bool $isBoolean): Gmail
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
     * @return Gmail
     */
    public function withLabelIds(array $labelIds): Gmail
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
     * @return Gmail
     */
    public function withMaxResults(int $maxResults): Gmail
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
     * @return Gmail
     */
    public function withEmail(string $email): Gmail
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
     * @return Gmail
     */
    public function withUserId($userId): Gmail
    {
        $this->userId = $userId;
        return $this;
    }
}
