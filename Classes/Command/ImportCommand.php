<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Command;

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

use Mediadreams\MdMastodon\Http\MastodonApiRequester;
use Mediadreams\MdMastodon\Service\ImagesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\Logger;
use \TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ImportCommand
 * @package Mediadreams\MdMastodon\Command
 */
class ImportCommand extends Command
{
    protected string $table = 'tx_mdmastodon_domain_model_configuration';
    protected MastodonApiRequester $mastodonApiRequester;
    protected ImagesService $imagesService;
    protected Logger $logger;

    /**
     * ImportFeedCommand constructor.
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->mastodonApiRequester = GeneralUtility::makeInstance(MastodonApiRequester::class);
        $this->imagesService = GeneralUtility::makeInstance(ImagesService::class);

        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->logger = $logManager->getLogger(self::class);
    }

    /**
     * Configuration of command
     */
    protected function configure(): void
    {
        $this->setHelp('This command imports configured Mastodon feeds.');
        $this->setDescription('Import Mastodon feed');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $configurations = $this->getConfigsForUpdate(time());

            if (count($configurations) > 0) {
                foreach ($configurations as $conf) {
                    $apiData = $this->mastodonApiRequester->request($conf);

                    if (!empty($apiData)) {
                        // Clear cache
                        $cachedPages = json_decode($conf['cached_in_pages'], true);
                        if (is_array($cachedPages)) {
                            $this->clearCachedPages($cachedPages);
                        }

                        // Load images for feed and set local image path for entries
                        $apiData = $this->imagesService->loadImages($apiData);

                        // Update configuration
                        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->table);
                        $queryBuilder
                            ->update($this->table)
                            ->where(
                                $queryBuilder->expr()->eq(
                                    'uid',
                                    $queryBuilder->createNamedParameter($conf['uid'], Connection::PARAM_INT)
                                )
                            )
                            ->set('data', $apiData)
                            ->set('cached_in_pages', json_encode([]))
                            ->set('import_date', time())
                            ->executeStatement();

                        $output->writeln('Data for configuration with Uid ' . $conf['uid'] . ' was successfully saved.');
                    }
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $this->logger->error('Import of Mastodon API call failed.', [
                'exeption' => $exception->getMessage()
            ]);

            $output->writeln('Error with Mastodon API');
            $output->writeln('Reason: ' . $exception->getMessage());

            return 1687957817;
        }
    }

    /**
     * Get configurations
     *
     * @param int $timestamp
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getConfigsForUpdate(int $timestamp): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($this->table);

        $result = $queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('(`import_date` + `update_frequency`) <= ' . $queryBuilder->createNamedParameter($timestamp, Connection::PARAM_INT));

        return $result->executeQuery()->fetchAllAssociative();
    }

    /**
     * Clear page cache for given page Ids
     *
     * @param array $pages Array with page Uids
     */
    private function clearCachedPages(array $pages): void
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        foreach ($pages as $page) {
            $cacheManager->flushCachesInGroupByTags('pages', [ 'pageId_' . $page ]);
        }
    }
}
