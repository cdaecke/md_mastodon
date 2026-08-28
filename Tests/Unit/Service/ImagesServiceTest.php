<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Unit\Service;

use Mediadreams\MdMastodon\Service\ImagesService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ImagesService::class)]
final class ImagesServiceTest extends UnitTestCase
{
    private ImagesService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ImagesService(
            self::createStub(RequestFactory::class),
            self::createStub(LoggerInterface::class),
        );
    }

    /**
     * @return array<string, array{0: array, 1: string|false}>
     */
    public static function itemProvider(): array
    {
        return [
            'image media attachment takes priority over card and reblog' => [
                [
                    'media_attachments' => [
                        ['type' => 'image', 'url' => 'https://example.com/media.jpg', 'preview_url' => 'https://example.com/media-preview.jpg'],
                    ],
                    'card' => ['image' => 'https://example.com/card.jpg'],
                    'reblog' => null,
                ],
                'https://example.com/media.jpg',
            ],
            'video media attachment uses preview_url instead of the video url' => [
                [
                    'media_attachments' => [
                        ['type' => 'video', 'url' => 'https://example.com/media.mp4', 'preview_url' => 'https://example.com/media-preview.jpg'],
                    ],
                    'card' => null,
                    'reblog' => null,
                ],
                'https://example.com/media-preview.jpg',
            ],
            'card image is used when there is no media attachment' => [
                [
                    'media_attachments' => [],
                    'card' => ['image' => 'https://example.com/card.jpg'],
                    'reblog' => null,
                ],
                'https://example.com/card.jpg',
            ],
            'reblog media attachment is used when there is neither media attachment nor card' => [
                [
                    'media_attachments' => [],
                    'card' => null,
                    'reblog' => [
                        'media_attachments' => [
                            ['type' => 'image', 'url' => 'https://example.com/reblog-media.jpg', 'preview_url' => 'https://example.com/reblog-media-preview.jpg'],
                        ],
                        'card' => ['image' => 'https://example.com/reblog-card.jpg'],
                    ],
                ],
                'https://example.com/reblog-media.jpg',
            ],
            'reblog video media attachment uses preview_url instead of the video url' => [
                [
                    'media_attachments' => [],
                    'card' => null,
                    'reblog' => [
                        'media_attachments' => [
                            ['type' => 'video', 'url' => 'https://example.com/reblog-media.mp4', 'preview_url' => 'https://example.com/reblog-media-preview.jpg'],
                        ],
                        'card' => null,
                    ],
                ],
                'https://example.com/reblog-media-preview.jpg',
            ],
            'reblog card image is used as last resort' => [
                [
                    'media_attachments' => [],
                    'card' => null,
                    'reblog' => [
                        'media_attachments' => [],
                        'card' => ['image' => 'https://example.com/reblog-card.jpg'],
                    ],
                ],
                'https://example.com/reblog-card.jpg',
            ],
            'no image source at all resolves to false' => [
                [
                    'media_attachments' => [],
                    'card' => null,
                    'reblog' => null,
                ],
                false,
            ],
        ];
    }

    #[Test]
    #[DataProvider('itemProvider')]
    public function resolveImageUrlPicksExpectedSource(array $item, string|false $expected): void
    {
        $reflectionMethod = new \ReflectionMethod(ImagesService::class, 'resolveImageUrl');

        self::assertSame($expected, $reflectionMethod->invoke($this->subject, $item));
    }
}
