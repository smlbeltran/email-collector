<?php

declare(strict_types=1);

namespace EmailCollector\Service\Gmail;


class Gmail
{
    private $labelIds = [];

    private $maxResults;

    private $query;

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
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     * @return Gmail
     */
    public function withQuery(string $query): Gmail
    {
        $this->query = $query;
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
