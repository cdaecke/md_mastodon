<?php
defined('TYPO3') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'MdMastodon',
    'Api',
    'Mastodon'
);

// Remove not needed `Record Storage Page` for plugin
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mdmastodon_api'] = 'recursive,select_key,pages';

// Add flexform for plugin
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mdmastodon_api'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'mdmastodon_api',
    'FILE:EXT:md_mastodon/Configuration/FlexForms/PluginApi.xml'
);
