<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Service;

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

use TYPO3\CMS\Core\Log\Logger;
use \TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class SettingsService
 * @package Mediadreams\MdMastodon\Service
 */
class SettingsService
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Injects the Configuration Manager and loads the settings
     *
     * @param ConfigurationManagerInterface $configurationManager An instance of the Configuration Manager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Returns all settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->logger = $logManager->getLogger(self::class);

        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        $settings = GeneralUtility::removeDotsFromTS($settings);

        if (!isset($settings['plugin']['tx_mdmastodon_api']['settings'])) {
            $this->logger->error('Typoscript of ext:md_mastodon could not be loaded!');
            return [];
        } else {
            return $settings['plugin']['tx_mdmastodon_api']['settings'];
        }
    }
}
