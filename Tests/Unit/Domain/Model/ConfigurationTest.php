<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Unit\Domain\Model;

use Mediadreams\MdMastodon\Domain\Model\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends UnitTestCase
{
    private Configuration $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Configuration();
    }

    #[Test]
    public function isAbstractEntity(): void
    {
        self::assertInstanceOf(AbstractEntity::class, $this->subject);
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->subject->setTitle('My Mastodon feed');

        self::assertSame('My Mastodon feed', $this->subject->getTitle());
    }

    #[Test]
    public function setApiUrlSetsApiUrl(): void
    {
        $this->subject->setApiUrl('https://mastodon.example/api/v1/');

        self::assertSame('https://mastodon.example/api/v1/', $this->subject->getApiUrl());
    }

    #[Test]
    public function setApiTokenSetsApiToken(): void
    {
        $this->subject->setApiToken('some-token');

        self::assertSame('some-token', $this->subject->getApiToken());
    }

    #[Test]
    public function setApiMethodSetsApiMethod(): void
    {
        $this->subject->setApiMethod('public_timeline');

        self::assertSame('public_timeline', $this->subject->getApiMethod());
    }

    #[Test]
    public function getAccountIdInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getAccountId());
    }

    #[Test]
    public function setAccountIdSetsAccountId(): void
    {
        $this->subject->setAccountId(123);

        self::assertSame(123, $this->subject->getAccountId());
    }

    #[Test]
    public function isOnlyMediaInitiallyReturnsFalse(): void
    {
        self::assertFalse($this->subject->isOnlyMedia());
    }

    #[Test]
    public function setOnlyMediaSetsOnlyMedia(): void
    {
        $this->subject->setOnlyMedia(true);

        self::assertTrue($this->subject->isOnlyMedia());
    }

    #[Test]
    public function isExcludeRepliesInitiallyReturnsFalse(): void
    {
        self::assertFalse($this->subject->isExcludeReplies());
    }

    #[Test]
    public function setExcludeRepliesSetsExcludeReplies(): void
    {
        $this->subject->setExcludeReplies(true);

        self::assertTrue($this->subject->isExcludeReplies());
    }

    #[Test]
    public function isExcludeReblogsInitiallyReturnsFalse(): void
    {
        self::assertFalse($this->subject->isExcludeReblogs());
    }

    #[Test]
    public function setExcludeReblogsSetsExcludeReblogs(): void
    {
        $this->subject->setExcludeReblogs(true);

        self::assertTrue($this->subject->isExcludeReblogs());
    }

    #[Test]
    public function isOnlyPinnedInitiallyReturnsFalse(): void
    {
        self::assertFalse($this->subject->isOnlyPinned());
    }

    #[Test]
    public function setOnlyPinnedSetsOnlyPinned(): void
    {
        $this->subject->setOnlyPinned(true);

        self::assertTrue($this->subject->isOnlyPinned());
    }

    #[Test]
    public function getHashtagInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getHashtag());
    }

    #[Test]
    public function setHashtagSetsHashtag(): void
    {
        $this->subject->setHashtag('typo3');

        self::assertSame('typo3', $this->subject->getHashtag());
    }

    #[Test]
    public function getListIdInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getListId());
    }

    #[Test]
    public function setListIdSetsListId(): void
    {
        $this->subject->setListId('42');

        self::assertSame('42', $this->subject->getListId());
    }

    #[Test]
    public function getUpdateFrequencyInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getUpdateFrequency());
    }

    #[Test]
    public function setUpdateFrequencySetsUpdateFrequency(): void
    {
        $this->subject->setUpdateFrequency(3600);

        self::assertSame(3600, $this->subject->getUpdateFrequency());
    }

    #[Test]
    public function getImportDateInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getImportDate());
    }

    #[Test]
    public function setImportDateSetsImportDate(): void
    {
        $date = new \DateTime('2026-01-01 12:00:00');
        $this->subject->setImportDate($date);

        self::assertSame($date, $this->subject->getImportDate());
    }

    #[Test]
    public function getDataInitiallyReturnsEmptyArray(): void
    {
        self::assertSame([], $this->subject->getData());
    }

    #[Test]
    public function getDataReturnsDecodedJsonData(): void
    {
        $this->subject->setData('[{"id":"1","content":"Hello"}]');

        self::assertSame([['id' => '1', 'content' => 'Hello']], $this->subject->getData());
    }

    #[Test]
    public function getCachedInPagesInitiallyReturnsEmptyJsonArray(): void
    {
        self::assertSame('[]', $this->subject->getCachedInPages());
    }

    #[Test]
    public function getCachedInPagesArrInitiallyReturnsEmptyArray(): void
    {
        self::assertSame([], $this->subject->getCachedInPagesArr());
    }

    #[Test]
    public function setCachedInPagesEncodesPagesAsJson(): void
    {
        $this->subject->setCachedInPages([1, 2, 3]);

        self::assertSame('[1,2,3]', $this->subject->getCachedInPages());
        self::assertSame([1, 2, 3], $this->subject->getCachedInPagesArr());
    }

    #[Test]
    public function resetCachedInPagesEmptiesPreviouslySetPages(): void
    {
        $this->subject->setCachedInPages([1, 2, 3]);
        $this->subject->resetCachedInPages();

        self::assertSame([], $this->subject->getCachedInPagesArr());
    }
}
