<?php

defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MdMastodon',
        'Api',
        [
            \Mediadreams\MdMastodon\Controller\ConfigurationController::class => 'show'
        ],
        // non-cacheable actions
        [
            \Mediadreams\MdMastodon\Controller\ConfigurationController::class => ''
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );
})();
