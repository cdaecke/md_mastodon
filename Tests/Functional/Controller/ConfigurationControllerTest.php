<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Functional\Controller;

use Mediadreams\MdMastodon\Controller\ConfigurationController;
use Mediadreams\MdMastodon\Domain\Repository\ConfigurationRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ConfigurationController::class)]
// Configuration's #[Validate(['validator' => ...])] array-form attribute is
// required for v13+v14 dual compatibility (see Domain/Model/Configuration.php)
// and triggers a soft v14 deprecation whenever Extbase reflects the class.
#[IgnoreDeprecations('Passing an array of configuration values to Extbase attributes')]
final class ConfigurationControllerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['mediadreams/md_mastodon'];

    protected array $coreExtensionsToLoad = ['install', 'scheduler', 'fluid_styled_content'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/md_mastodon/Tests/Functional/Controller/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
                'EXT:md_mastodon/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:md_mastodon/Configuration/TypoScript/setup.typoscript',
                'EXT:md_mastodon/Tests/Functional/Controller/Fixtures/TypoScript/Setup/Rendering.typoscript',
            ],
        ]);

        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/ContentElement.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/Configuration.csv');
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function seedConfigurationData(array $items): void
    {
        $configurationRepository = $this->get(ConfigurationRepository::class);
        $configuration = $configurationRepository->findByUid(1);
        $configuration->setData(json_encode($items));
        $configurationRepository->update($configuration);

        $this->get(PersistenceManagerInterface::class)->persistAll();
    }

    /**
     * @return array<string, mixed>
     */
    private function createItem(string $id, string $text): array
    {
        return [
            'id' => $id,
            'content' => '<p>' . $text . '</p>',
            'url' => 'https://example.com/' . $id,
            'account' => ['acct' => 'user@example.com'],
            'created_at' => '2026-01-01T12:00:00.000Z',
            'card' => false,
            'reblog' => false,
        ];
    }

    #[Test]
    public function showActionRendersNoItemsMessageWhenConfigurationHasNoData(): void
    {
        $request = (new InternalRequest())->withPageId(1);

        $html = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('No toots found.', $html);
    }

    #[Test]
    public function showActionRendersAvailableItems(): void
    {
        $this->seedConfigurationData([
            $this->createItem('1', 'Toot number 1'),
            $this->createItem('2', 'Toot number 2'),
        ]);

        $request = (new InternalRequest())->withPageId(1);
        $html = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('Toot number 1', $html);
        self::assertStringContainsString('Toot number 2', $html);
    }

    #[Test]
    public function showActionSlicesDataToConfiguredLimit(): void
    {
        $this->seedConfigurationData([
            $this->createItem('1', 'Toot number 1'),
            $this->createItem('2', 'Toot number 2'),
            $this->createItem('3', 'Toot number 3'),
            $this->createItem('4', 'Toot number 4'),
            $this->createItem('5', 'Toot number 5'),
        ]);

        $request = (new InternalRequest())->withPageId(1);
        $html = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('Toot number 1', $html);
        self::assertStringContainsString('Toot number 2', $html);
        self::assertStringNotContainsString('Toot number 3', $html);
    }

    #[Test]
    public function showActionAddsCurrentPageToCachedInPages(): void
    {
        $this->seedConfigurationData([$this->createItem('1', 'Toot number 1')]);

        $request = (new InternalRequest())->withPageId(1);
        $this->executeFrontendSubRequest($request);

        $this->assertCSVDataSet(__DIR__ . '/Fixtures/Database/ConfigurationCachedOnPage1.csv');
    }

    #[Test]
    public function showActionDoesNotDuplicatePageInCachedInPagesOnRepeatedRendering(): void
    {
        $this->seedConfigurationData([$this->createItem('1', 'Toot number 1')]);

        $request = (new InternalRequest())->withPageId(1);
        $this->executeFrontendSubRequest($request);
        $this->executeFrontendSubRequest($request);

        $this->assertCSVDataSet(__DIR__ . '/Fixtures/Database/ConfigurationCachedOnPage1.csv');
    }
}
