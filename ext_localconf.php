<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Mediadreams\MdMastodon\Controller\ConfigurationController;

defined('TYPO3') || die();

(static function () {
    ExtensionUtility::configurePlugin(
        'MdMastodon',
        'Api',
        [
            ConfigurationController::class => 'show'
        ],
        // non-cacheable actions
        [
            ConfigurationController::class => ''
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );
})();
