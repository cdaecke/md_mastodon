<?php

declare(strict_types=1);

use a9f\Fractor\Configuration\FractorConfiguration;
use a9f\Typo3Fractor\Set\Typo3LevelSetList;

// Same UP_TO_TYPO3_13 rationale as Build/rector/config.php: avoid v14-only
// migrations that would break the v13.4 side of the dual-compatibility range.
return FractorConfiguration::configure()
    ->withPaths([
        __DIR__ . '/../../Configuration/',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ]);
