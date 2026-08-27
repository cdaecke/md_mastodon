<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Functional\Command;

use Mediadreams\MdMastodon\Command\ImportCommand;
use Mediadreams\MdMastodon\Http\MastodonApiRequester;
use Mediadreams\MdMastodon\Service\ImagesService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ImportCommand::class)]
final class ImportCommandTest extends FunctionalTestCase
{
    private const COMMAND_NAME = 'mdmastodon:import';

    protected array $testExtensionsToLoad = ['mediadreams/md_mastodon'];

    protected array $coreExtensionsToLoad = ['install', 'scheduler'];

    private ConnectionPool $connectionPool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connectionPool = $this->get(ConnectionPool::class);
    }

    private function createCommandTester(RequestFactory $requestFactory, CacheManager $cacheManager): CommandTester
    {
        $logger = new NullLogger();
        $subject = new ImportCommand(
            new MastodonApiRequester($requestFactory, $logger),
            new ImagesService($requestFactory, $logger),
            $logger,
            $this->connectionPool,
            $cacheManager,
            self::COMMAND_NAME,
        );

        $application = new Application();
        $application->add($subject);

        return new CommandTester($application->find(self::COMMAND_NAME));
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function createSuccessfulApiResponse(array $items): ResponseInterface
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode($items));

        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchConfigurationRow(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_mdmastodon_domain_model_configuration');

        return $queryBuilder
            ->select('*')
            ->from('tx_mdmastodon_domain_model_configuration')
            ->where($queryBuilder->expr()->eq('uid', 1))
            ->executeQuery()
            ->fetchAssociative();
    }

    #[Test]
    public function isConsoleCommand(): void
    {
        $requestFactory = self::createStub(RequestFactory::class);
        $logger = new NullLogger();

        self::assertInstanceOf(Command::class, new ImportCommand(
            new MastodonApiRequester($requestFactory, $logger),
            new ImagesService($requestFactory, $logger),
            $logger,
            $this->connectionPool,
            self::createStub(CacheManager::class),
        ));
    }

    #[Test]
    public function runReturnsSuccessStatusAndUpdatesDataImportDateAndCachedInPages(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/Configuration.csv');

        /** @var RequestFactory&MockObject $requestFactory */
        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->once())
            ->method('request')
            ->willReturn($this->createSuccessfulApiResponse([
                [
                    'id' => '1',
                    'content' => 'Toot 1',
                    'account' => ['acct' => 'user@example.com'],
                    'created_at' => '2026-01-01T12:00:00.000Z',
                    'card' => false,
                    'reblog' => false,
                    'media_attachments' => [],
                ],
            ]));

        /** @var CacheManager&MockObject $cacheManager */
        $cacheManager = $this->createMock(CacheManager::class);
        $flushedTags = [];
        $cacheManager->expects($this->exactly(2))
            ->method('flushCachesInGroupByTags')
            ->willReturnCallback(function (string $cacheGroup, array $tags) use (&$flushedTags): void {
                self::assertSame('pages', $cacheGroup);
                $flushedTags[] = $tags[0];
            });

        $beforeImport = time();
        $result = $this->createCommandTester($requestFactory, $cacheManager)->execute([]);

        self::assertSame(Command::SUCCESS, $result);
        self::assertSame(['pageId_1', 'pageId_2'], $flushedTags);

        $row = $this->fetchConfigurationRow();
        self::assertSame('[]', $row['cached_in_pages']);
        self::assertGreaterThanOrEqual($beforeImport, (int)$row['import_date']);

        $data = json_decode($row['data'], true);
        self::assertSame('Toot 1', $data[0]['content']);
    }

    #[Test]
    public function runDoesNotCallApiForConfigurationsNotYetDueForUpdate(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/ConfigurationNotDue.csv');

        /** @var RequestFactory&MockObject $requestFactory */
        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->never())->method('request');

        /** @var CacheManager&MockObject $cacheManager */
        $cacheManager = $this->createMock(CacheManager::class);
        $cacheManager->expects($this->never())->method('flushCachesInGroupByTags');

        $result = $this->createCommandTester($requestFactory, $cacheManager)->execute([]);

        self::assertSame(Command::SUCCESS, $result);
        $this->assertCSVDataSet(__DIR__ . '/Fixtures/Database/ConfigurationNotDue.csv');
    }
}
