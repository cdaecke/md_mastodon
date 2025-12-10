<?php
defined('TYPO3') || die();

$frontendPluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'MdMastodon',
    'Api',
    'Mastodon',
    null,
    null,
    'Show configured Mastodon items (toots).'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,pi_flexform',
    $frontendPluginSignature,
    'after:palette:headers',
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '',
    'FILE:EXT:md_mastodon/Configuration/FlexForms/PluginApi.xml',
    $frontendPluginSignature,
);
