<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Domain\Repository;

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

use Mediadreams\MdMastodon\Domain\Model\Configuration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * The repository for Configurations
 */
class ConfigurationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $table = 'tx_mdmastodon_domain_model_configuration';

    /**
     * Initialize repository
     */
    public function initializeObject()
    {
        /** @var QuerySettingsInterface $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);

        // Show configurations from all pages
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param $timestamp
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getConfigsForUpdate(int $timestamp)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->table);

        $queryBuilder = $connection->createQueryBuilder();
        $query = $queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('(`import_date` + `update_frequency`) <= ' . $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT));

        $result = $query->execute()->fetchAllAssociative();

        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        $objects = $dataMapper->map(Configuration::class, $result);

        return $objects;
    }
}
