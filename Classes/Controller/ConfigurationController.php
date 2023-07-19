<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Controller;


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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * ConfigurationController
 */
class ConfigurationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * configurationRepository
     *
     * @var ConfigurationRepository
     */
    protected $configurationRepository = null;

    /**
     * @param ConfigurationRepository $configurationRepository
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * action show
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(): \Psr\Http\Message\ResponseInterface
    {
        /** @var \Mediadreams\MdMastodon\Domain\Model\Configuration $configuration */
        $configuration = $this->configurationRepository->findByUid($this->settings['configId']);

        $cachedInPages = $configuration->getCachedInPages();
        if (!in_array($this->getTypoScriptFrontendController()->id, $cachedInPages)) {
            // Add page id to $cachedInPages
            array_push($cachedInPages, $this->getTypoScriptFrontendController()->id);
            $configuration->setCachedInPages($cachedInPages);
            $this->configurationRepository->update($configuration);
        }

        $this->view->assign('items', $configuration->getData());
        return $this->htmlResponse();
    }

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
