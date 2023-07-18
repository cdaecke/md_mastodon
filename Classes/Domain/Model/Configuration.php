<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Domain\Model;

/**
 * This file is part of the "Mastodon social networking API" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Christoph Daecke <typo3@mediadreams.org>
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Configuration
 */
class Configuration extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Title of the configuration
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = null;

    /**
     * The Mastodon Api url
     *
     * @var string
     */
    protected $apiUrl = null;

    /**
     * Mastodon API token
     *
     * @var string
     */
    protected $apiToken = null;

    /**
     * Mastodon API method
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $apiMethod = null;

    /**
     * Mastodon account ID
     *
     * @var int
     */
    protected $accountId = null;

    /**
     * @var bool
     */
    protected $onlyMedia = false;

    /**
     * @var bool
     */
    protected $excludeReplies = false;

    /**
     * @var bool
     */
    protected $excludeReblogs = false;

    /**
     * @var bool
     */
    protected $onlyPinned = false;

    /**
     * Mastodon API hashtag
     *
     * @var string
     */
    protected $hashtag = null;

    /**
     * Mastodon API list ID
     *
     * @var string
     */
    protected $listId = null;

    /**
     * Update frequency in seconds
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $updateFrequency = null;

    /**
     * Date of last update
     *
     * @var \DateTime
     */
    protected $importDate = null;

    /**
     * JSON response of the api call
     *
     * @var string
     */
    protected $data = null;

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the apiUrl
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Sets the apiUrl
     *
     * @param string $apiUrl
     * @return void
     */
    public function setApiUrl(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Returns the apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Sets the apiToken
     *
     * @param string $apiToken
     * @return void
     */
    public function setApiToken(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Returns the apiMethod
     *
     * @return string
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * Sets the apiMethod
     *
     * @param string $apiMethod
     * @return void
     */
    public function setApiMethod(string $apiMethod)
    {
        $this->apiMethod = $apiMethod;
    }

    /**
     * Returns the accountId
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Sets the accountId
     *
     * @param int $accountId
     * @return void
     */
    public function setAccountId(int $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * Get only media
     *
     * @return bool
     */
    public function getOnlyMedia(): bool
    {
        return $this->onlyMedia;
    }

    /**
     * Set only media
     *
     * @param bool $onlyMedia
     * @return void
     */
    public function setOnlyMedia($onlyMedia): void
    {
        $this->onlyMedia = $onlyMedia;
    }

    /**
     * Get exclude replies
     *
     * @return bool
     */
    public function getExcludeReplies(): bool
    {
        return $this->excludeReplies;
    }

    /**
     * Set exclude replies
     *
     * @param bool $excludeReplies
     * @return void
     */
    public function setExcludeReplies($excludeReplies): void
    {
        $this->excludeReplies = $excludeReplies;
    }

    /**
     * Get exclude reblogs
     *
     * @return bool
     */
    public function getExcludeReblogs(): bool
    {
        return $this->excludeReblogs;
    }

    /**
     * Set exclude reblogs
     *
     * @param bool $excludeReblogs
     * @return void
     */
    public function setExcludeReblogs($excludeReblogs): void
    {
        $this->excludeReblogs = $excludeReblogs;
    }

    /**
     * Get hashtag
     *
     * @return string
     */
    public function getHashtag(): string
    {
        return $this->hashtag;
    }

    /**
     * Set hashtag
     *
     * @param string $hashtag
     * @return void
     */
    public function setHashtag($hashtag): void
    {
        $this->hashtag = $hashtag;
    }

    /**
     * Get list ID
     *
     * @return string
     */
    public function getListId(): string
    {
        return $this->listId;
    }

    /**
     * Set list Id
     *
     * @param string $listId
     * @return void
     */
    public function setListId($listId): void
    {
        $this->listId = $listId;
    }

    /**
     * Get only pinned
     *
     * @return bool
     */
    public function getOnlyPinned(): bool
    {
        return $this->onlyPinned;
    }

    /**
     * Set only pinned
     *
     * @param bool $onlyPinned
     * @return void
     */
    public function setOnlyPinned($onlyPinned): void
    {
        $this->onlyPinned = $onlyPinned;
    }

    /**
     * Returns the updateFrequency
     *
     * @return int
     */
    public function getUpdateFrequency(): int
    {
        return $this->updateFrequency;
    }

    /**
     * Sets the updateFrequency
     *
     * @param int $updateFrequency
     * @return void
     */
    public function setUpdateFrequency(int $updateFrequency)
    {
        $this->updateFrequency = $updateFrequency;
    }

    /**
     * Returns the importDate
     *
     * @return \DateTime
     */
    public function getImportDate()
    {
        return $this->importDate;
    }

    /**
     * Sets the importDate
     *
     * @param int $importDate
     * @return void
     */
    public function setImportDate(int $importDate)
    {
        $this->importDate = $importDate;
    }

    /**
     * Returns the data
     *
     * @return array|string
     */
    public function getData()
    {
        return json_decode($this->data, true);
    }

    /**
     * Sets the data
     *
     * @param string $data
     * @return void
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
}
