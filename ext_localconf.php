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
        ]
    );
})();
