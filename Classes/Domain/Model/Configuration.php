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
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Configuration
 */
class Configuration extends AbstractEntity
{
    /**
     * Title of the configuration
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $title;

    /**
     * The Mastodon Api url
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $apiUrl;

    /**
     * Mastodon API token
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $apiToken;

    /**
     * Mastodon API method
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $apiMethod;

    /**
     * The ID of the Account in the database
     *
     * @var int|null
     */
    protected ?int $accountId = null;

    /**
     * Show only statuses with media attached
     *
     * @var bool
     */
    protected bool $onlyMedia = false;

    /**
     * Filter out statuses in reply to a different account
     *
     * @var bool
     */
    protected bool $excludeReplies = false;

    /**
     * Filter out boosts from the response
     *
     * @var bool
     */
    protected bool $excludeReblogs = false;

    /**
     * Filter for pinned statuses only
     *
     * @var bool
     */
    protected bool $onlyPinned = false;

    /**
     * Mastodon API hashtag
     *
     * @var string|null
     */
    protected ?string $hashtag = null;

    /**
     * Mastodon API list ID
     *
     * @var string|null
     */
    protected ?string $listId = null;

    /**
     * Update frequency in seconds
     *
     * @var int|null
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?int $updateFrequency = null;

    /**
     * Date of last update
     *
     * @var \DateTime|null
     */
    protected ?\DateTime $importDate = null;

    /**
     * JSON response of the api call
     *
     * @var string
     */
    protected string $data = '';

    /**
     * Comma seperated page Uids, where feed is cached
     *
     * @var string
     */
    protected string $cachedInPages = '[]';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    public function getApiMethod(): string
    {
        return $this->apiMethod;
    }

    public function setApiMethod(string $apiMethod): void
    {
        $this->apiMethod = $apiMethod;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function isOnlyMedia(): bool
    {
        return $this->onlyMedia;
    }

    public function setOnlyMedia(bool $onlyMedia): void
    {
        $this->onlyMedia = $onlyMedia;
    }

    public function isExcludeReplies(): bool
    {
        return $this->excludeReplies;
    }

    public function setExcludeReplies(bool $excludeReplies): void
    {
        $this->excludeReplies = $excludeReplies;
    }

    public function isExcludeReblogs(): bool
    {
        return $this->excludeReblogs;
    }

    public function setExcludeReblogs(bool $excludeReblogs): void
    {
        $this->excludeReblogs = $excludeReblogs;
    }

    public function isOnlyPinned(): bool
    {
        return $this->onlyPinned;
    }

    public function setOnlyPinned(bool $onlyPinned): void
    {
        $this->onlyPinned = $onlyPinned;
    }

    public function getHashtag(): ?string
    {
        return $this->hashtag;
    }

    public function setHashtag(?string $hashtag): void
    {
        $this->hashtag = $hashtag;
    }

    public function getListId(): ?string
    {
        return $this->listId;
    }

    public function setListId(?string $listId): void
    {
        $this->listId = $listId;
    }

    public function getUpdateFrequency(): ?int
    {
        return $this->updateFrequency;
    }

    public function setUpdateFrequency(?int $updateFrequency): void
    {
        $this->updateFrequency = $updateFrequency;
    }

    public function getImportDate(): ?\DateTime
    {
        return $this->importDate;
    }

    public function setImportDate(?\DateTime $importDate): void
    {
        $this->importDate = $importDate;
    }

    public function getData(): array|string
    {
        return json_decode($this->data, true) ?? [];
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getCachedInPages(): string
    {
        return $this->cachedInPages;
    }

    public function getCachedInPagesArr(): array
    {
        return json_decode($this->cachedInPages, true) ?? [];
    }

    public function setCachedInPages(array $cachedInPages): void
    {
        $this->cachedInPages = json_encode($cachedInPages);
    }

    public function resetCachedInPages(): void
    {
        $this->cachedInPages = json_encode([]);
    }
}
