<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Functional\Hooks;

use Mediadreams\MdMastodon\Hooks\TemplateLayouts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(TemplateLayouts::class)]
final class TemplateLayoutsTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['mediadreams/md_mastodon'];

    protected array $coreExtensionsToLoad = ['install', 'scheduler'];

    private TemplateLayouts $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new TemplateLayouts();
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('en');
    }

    #[Test]
    public function userTemplateLayoutLeavesItemsUnchangedWithoutPageTsConfig(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/PageWithoutTemplateLayouts.csv');

        $config = [
            'flexParentDatabaseRow' => ['pid' => 1],
            'items' => [],
        ];

        $this->subject->user_templateLayout($config);

        self::assertSame([], $config['items']);
    }

    #[Test]
    public function userTemplateLayoutAddsLayoutsConfiguredInPageTsConfig(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/PageWithTemplateLayouts.csv');

        $config = [
            'flexParentDatabaseRow' => ['pid' => 1],
            'items' => [],
        ];

        $this->subject->user_templateLayout($config);

        self::assertSame(
            [
                ['label' => 'Special', 'value' => 10],
                ['label' => 'Minimal', 'value' => 20],
            ],
            $config['items'],
        );
    }
}
