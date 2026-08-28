<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Unit\ViewHelpers;

use Mediadreams\MdMastodon\ViewHelpers\SafeUrlViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(SafeUrlViewHelper::class)]
final class SafeUrlViewHelperTest extends UnitTestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function urlProvider(): array
    {
        return [
            'https url is kept' => ['https://mastodon.example/@user/123', 'https://mastodon.example/@user/123'],
            'http url is kept' => ['http://mastodon.example/@user/123', 'http://mastodon.example/@user/123'],
            'javascript scheme is rejected' => ['javascript:alert(1)', ''],
            'data scheme is rejected' => ['data:text/html,<script>alert(1)</script>', ''],
            'protocol-relative url is rejected' => ['//evil.example/x', ''],
            'empty value is rejected' => ['', ''],
        ];
    }

    #[Test]
    #[DataProvider('urlProvider')]
    public function renderReturnsExpectedValue(string $value, string $expected): void
    {
        $subject = new SafeUrlViewHelper();
        $subject->setArguments(['value' => $value]);

        self::assertSame($expected, $subject->render());
    }
}
