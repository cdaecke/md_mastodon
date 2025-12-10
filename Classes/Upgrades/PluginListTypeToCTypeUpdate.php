<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('mdmastodonExtensionPluginListTypeToCTypeUpdate')]
final class PluginListTypeToCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'mdmastodon_api' => 'mdmastodon_api',
        ];
    }

    public function getTitle(): string
    {
        return 'EXT:md_mastodon: Migrate plugins';
    }

    public function getDescription(): string
    {
        return 'Migrates mdmastodon_api from list_type to CType.';
    }
}
