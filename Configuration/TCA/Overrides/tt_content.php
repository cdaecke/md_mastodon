<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$frontendPluginSignature = ExtensionUtility::registerPlugin(
    extensionName: 'MdMastodon',
    pluginName: 'Api',
    pluginTitle: 'Mastodon',
    pluginDescription: 'Show configured Mastodon items (toots).',
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,pi_flexform',
    $frontendPluginSignature,
    'after:palette:headers',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '',
    'FILE:EXT:md_mastodon/Configuration/FlexForms/PluginApi.xml',
    $frontendPluginSignature,
);
