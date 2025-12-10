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
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * ConfigurationController
 */
class ConfigurationController extends ActionController
{
    protected ConfigurationRepository $configurationRepository;

    /**
     * @param ConfigurationRepository $configurationRepository
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository): void
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * action show
     *
     * @return ResponseInterface
     */
    public function showAction(): ResponseInterface
    {
        /** @var \Mediadreams\MdMastodon\Domain\Model\Configuration $configuration */
        $configuration = $this->configurationRepository->findByUid($this->settings['configId']);

        $cachedInPages = $configuration->getCachedInPagesArr();
        if (!in_array($this->getTypoScriptFrontendController()->id, $cachedInPages)) {
            // Add page id to $cachedInPages
            $cachedInPages[] = $this->getTypoScriptFrontendController()->id;
            $configuration->setCachedInPages($cachedInPages);
            $this->configurationRepository->update($configuration);
        }

        $data = is_array($configuration->getData())? $configuration->getData():[];
        $data = array_slice($data, 0, (int)$this->settings['limit']);

        $this->view->assign('items', $data);
        return $this->htmlResponse();
    }

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $this->request->getAttribute('frontend.controller') ?? null;
    }
}
