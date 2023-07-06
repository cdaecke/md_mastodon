<?php

defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_mdmastodon_domain_model_configuration',
        'EXT:md_mastodon/Resources/Private/Language/locallang_csh_tx_mdmastodon_domain_model_configuration.xlf'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
        'tx_mdmastodon_domain_model_configuration'
    );
})();
