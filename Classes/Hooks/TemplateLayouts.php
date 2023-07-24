<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Hooks;

/**
 * This file is part of the "Mastodon social networking API" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Christoph Daecke <typo3@mediadreams.org>
 * This class was initially taken from ext:sf_event_mgt. Thanks to Torben Hansen
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Hook for Template Layouts
 */
class TemplateLayouts
{
    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     */
    public function user_templateLayout(array &$config): void
    {
        $templateLayouts = $this->getTemplateLayoutsFromTsConfig($config['flexParentDatabaseRow']['pid']);
        foreach ($templateLayouts as $index => $layout) {
            $additionalLayout = [
                $GLOBALS['LANG']->sL($layout),
                $index,
            ];
            $config['items'][] = $additionalLayout;
        }
    }

    /**
     * Get template layouts defined in TsConfig
     */
    protected function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_mdmastodon_api.']['templateLayouts.']) &&
            is_array($pagesTsConfig['tx_mdmastodon_api.']['templateLayouts.'])
        ) {
            $templateLayouts = $pagesTsConfig['tx_mdmastodon_api.']['templateLayouts.'];
        }

        return $templateLayouts;
    }
}
