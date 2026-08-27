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
use Mediadreams\MdMastodon\Domain\Model\Configuration;
use Mediadreams\MdMastodon\Domain\Repository\ConfigurationRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * ConfigurationController
 */
class ConfigurationController extends ActionController
{
    public function __construct(protected ConfigurationRepository $configurationRepository)
    {
    }
    /**
     * action show
     *
     * @return ResponseInterface
     */
    public function showAction(): ResponseInterface
    {
        /** @var Configuration $configuration */
        $configuration = $this->configurationRepository->findByUid($this->settings['configId']);

        $cachedInPages = $configuration->getCachedInPagesArr();
        $pageId = $this->request->getAttribute('frontend.page.information')->getId();
        if (!in_array($pageId, $cachedInPages)) {
            // Add page id to $cachedInPages
            $cachedInPages[] = $pageId;
            $configuration->setCachedInPages($cachedInPages);
            $this->configurationRepository->update($configuration);
        }

        $data = is_array($configuration->getData())? $configuration->getData():[];
        $data = array_slice($data, 0, (int)$this->settings['limit']);

        $this->view->assign('items', $data);
        return $this->htmlResponse();
    }
}
