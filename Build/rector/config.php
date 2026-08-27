<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Ssch\TYPO3Rector\TYPO313\v0\MigrateTypoScriptFrontendControllerReadOnlyPropertiesRector;

// Intentionally UP_TO_TYPO3_13 (not _14): keeps Rector from introducing
// v14-only APIs that would break the v13.4 side of the dual-compatibility
// range this extension supports.
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../../Classes/',
        __DIR__ . '/../../Configuration/',
        __DIR__ . '/../../ext_emconf.php',
        __DIR__ . '/../../ext_localconf.php',
    ])
    ->withPhpSets()
    ->withComposerBased(phpunit: true)
    ->withSets([
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ])
    ->withImportNames(true, true, false)
    // Rewrites the removed `frontend.controller` request attribute access to
    // $GLOBALS['TYPO3_REQUEST'] instead of $this->request. Fixed manually
    // instead, using $this->request directly (see ConfigurationController).
    ->withSkip([
        MigrateTypoScriptFrontendControllerReadOnlyPropertiesRector::class,
    ]);
