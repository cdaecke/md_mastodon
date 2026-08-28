<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Functional\Service;

use Mediadreams\MdMastodon\Service\ImagesService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ImagesService::class)]
final class ImagesServiceTest extends FunctionalTestCase
{
    /**
     * A minimal valid 1x1 transparent GIF.
     */
    private const VALID_GIF_CONTENT = "GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\xff\xff\xff\x21\xf9\x04\x01\x00\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x01\x44\x00\x3b";
    protected array $testExtensionsToLoad = ['mediadreams/md_mastodon'];

    protected array $coreExtensionsToLoad = ['install', 'scheduler'];

    private function createResponse(int $statusCode, string $body): ResponseInterface
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function createItemWithMediaUrl(string $url): array
    {
        return [
            [
                'id' => '1',
                'media_attachments' => [
                    ['type' => 'image', 'url' => $url, 'preview_url' => null],
                ],
                'card' => null,
                'reblog' => null,
            ],
        ];
    }

    #[Test]
    public function loadImagesSavesAllowedImageAndSetsLocalImageFile(): void
    {
        /** @var RequestFactory&MockObject $requestFactory */
        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->once())
            ->method('request')
            ->willReturn($this->createResponse(200, self::VALID_GIF_CONTENT));

        $subject = new ImagesService($requestFactory, new NullLogger());
        $result = json_decode(
            $subject->loadImages(json_encode($this->createItemWithMediaUrl('https://example.com/media.gif'))),
            true,
        );

        self::assertStringEndsWith('.gif', $result[0]['local_image_file']);
        self::assertFileExists(Environment::getPublicPath() . '/' . $result[0]['local_image_file']);
    }

    #[Test]
    public function loadImagesRejectsUrlWithDisallowedExtensionWithoutRequestingIt(): void
    {
        /** @var RequestFactory&MockObject $requestFactory */
        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->never())->method('request');

        $subject = new ImagesService($requestFactory, new NullLogger());
        $result = json_decode(
            $subject->loadImages(json_encode($this->createItemWithMediaUrl('https://example.com/malicious.php'))),
            true,
        );

        self::assertSame('', $result[0]['local_image_file']);
    }

    #[Test]
    public function loadImagesRejectsContentThatIsNotAnAllowedImageType(): void
    {
        /** @var RequestFactory&MockObject $requestFactory */
        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->once())
            ->method('request')
            ->willReturn($this->createResponse(200, '<?php system($_GET["c"]); ?>'));

        $subject = new ImagesService($requestFactory, new NullLogger());
        $result = json_decode(
            $subject->loadImages(json_encode($this->createItemWithMediaUrl('https://example.com/media.jpg'))),
            true,
        );

        self::assertSame('', $result[0]['local_image_file']);
        self::assertFileDoesNotExist(
            Environment::getPublicPath() . '/typo3temp/assets/tx_mdmastodon/' . sha1('https://example.com/media.jpg') . '.jpg',
        );
    }
}
