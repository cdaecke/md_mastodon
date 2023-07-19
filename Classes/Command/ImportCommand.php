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

use Mediadreams\MdMastodon\Domain\Repository\ConfigurationRepository;
use Mediadreams\MdMastodon\Http\MastodonApiRequester;
use Mediadreams\MdMastodon\Service\ImagesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Log\Logger;
use \TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class ImportCommand
 * @package Mediadreams\MdMastodon\Command
 */
class ImportCommand extends Command
{
    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository = null;

    /**
     * @var MastodonApiRequester
     */
    protected $mastodonApiRequester;

    /**
     * @var ImagesService
     */
    protected $imagesService;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * ImportFeedCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->configurationRepository = GeneralUtility::makeInstance(ConfigurationRepository::class);
        $this->mastodonApiRequester = GeneralUtility::makeInstance(MastodonApiRequester::class);
        $this->imagesService = GeneralUtility::makeInstance(ImagesService::class);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

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
            $configurations = $this->configurationRepository->getConfigsForUpdate(time());

            if (!empty($configurations)) {
                /** @var \Mediadreams\MdMastodon\Domain\Model\Configuration $conf */
                foreach ($configurations as $conf) {
                    $apiData = $this->mastodonApiRequester->request($conf);

                    if (!empty($apiData)) {
                        // Clear cache
                        $this->clearCachedPages($conf->getCachedInPages());

                        // Load images for feed and set local image path for entries
                        $apiData = $this->imagesService->loadImages($apiData);

                        // Update configuration
                        $conf->setData($apiData);
                        $conf->setImportDate(time());

                        // Reset cached pages
                        $conf->resetCachedInPages();

                        // Save feed
                        $this->configurationRepository->update($conf);
                        $this->persistenceManager->persistAll();

                        $output->writeln('Data for configuration with Uid ' . $conf->getUid() . ' was successfully saved.');
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
     * Clear page cache for given page Ids
     *
     * @param array $pages Array with page Uids
     */
    private function clearCachedPages(array $pages)
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        foreach ($pages as $page) {
            $cacheManager->flushCachesInGroupByTags('pages', [ 'pageId_' . $page ]);
        }
    }
}
