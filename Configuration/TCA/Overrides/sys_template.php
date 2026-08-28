<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile(
    'md_mastodon',
    'Configuration/TypoScript',
    'Mastodon social networking API'
);
