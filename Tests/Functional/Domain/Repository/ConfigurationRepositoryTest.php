<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Functional\Domain\Repository;

use Mediadreams\MdMastodon\Domain\Model\Configuration;
use Mediadreams\MdMastodon\Domain\Repository\ConfigurationRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ConfigurationRepository::class)]
#[CoversClass(Configuration::class)]
// Configuration's #[Validate(['validator' => ...])] array-form attribute is
// required for v13+v14 dual compatibility (see Domain/Model/Configuration.php)
// and triggers a soft v14 deprecation whenever Extbase reflects the class.
#[IgnoreDeprecations('Passing an array of configuration values to Extbase attributes')]
final class ConfigurationRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['mediadreams/md_mastodon'];

    protected array $coreExtensionsToLoad = ['install', 'scheduler'];

    private ConfigurationRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->get(ConfigurationRepository::class);
    }

    #[Test]
    public function isRepository(): void
    {
        self::assertInstanceOf(Repository::class, $this->subject);
    }

    #[Test]
    public function findByUidForInexistentRecordReturnsNull(): void
    {
        self::assertNull($this->subject->findByUid(1));
    }

    #[Test]
    public function findByUidForExistingRecordReturnsModel(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByUid/Configuration.csv');

        self::assertInstanceOf(Configuration::class, $this->subject->findByUid(1));
    }

    #[Test]
    public function findByUidMapsScalarData(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByUid/Configuration.csv');

        $model = $this->subject->findByUid(1);
        self::assertInstanceOf(Configuration::class, $model);

        self::assertSame('My feed', $model->getTitle());
        self::assertSame('https://mastodon.example/api/v1/', $model->getApiUrl());
        self::assertSame('public_timeline', $model->getApiMethod());
        self::assertSame(3600, $model->getUpdateFrequency());
        self::assertTrue($model->isOnlyMedia());
    }

    #[Test]
    public function findByUidFindsRecordRegardlessOfItsStoragePage(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByUid/ConfigurationOnDifferentPage.csv');

        $model = $this->subject->findByUid(1);

        self::assertInstanceOf(Configuration::class, $model);
        self::assertSame('Feed on a different page', $model->getTitle());
    }

    #[Test]
    public function updatePersistsChangesToCachedInPages(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByUid/Configuration.csv');

        $model = $this->subject->findByUid(1);
        self::assertInstanceOf(Configuration::class, $model);

        $model->setCachedInPages([1, 2]);
        $this->subject->update($model);
        $this->get(PersistenceManagerInterface::class)->persistAll();

        $this->assertCSVDataSet(__DIR__ . '/Fixtures/findByUid/ConfigurationAfterUpdate.csv');
    }
}
