<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Tests\Unit\Http;

use Mediadreams\MdMastodon\Http\MastodonApiRequester;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(MastodonApiRequester::class)]
final class MastodonApiRequesterTest extends UnitTestCase
{
    private RequestFactory&MockObject $requestFactory;
    private LoggerInterface&Stub $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestFactory = $this->createMock(RequestFactory::class);
        $this->logger = self::createStub(LoggerInterface::class);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function createBaseConf(array $overrides = []): array
    {
        return array_merge([
            'uid' => 1,
            'api_url' => 'https://mastodon.example/api/v1/',
            'api_token' => 'some-token',
            'api_method' => 'public_timeline',
            'account_id' => 0,
            'list_id' => '',
            'hashtag' => '',
            'only_media' => false,
            'exclude_replies' => false,
            'exclude_reblogs' => false,
            'only_pinned' => false,
        ], $overrides);
    }

    private function createSubject(?LoggerInterface $logger = null): MastodonApiRequester
    {
        return new MastodonApiRequester($this->requestFactory, $logger ?? $this->logger);
    }

    #[Test]
    public function requestReturnsEmptyStringAndLogsErrorWhenApiTokenIsMissing(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $this->requestFactory->expects($this->never())->method('request');

        self::assertSame('', $this->createSubject($logger)->request($this->createBaseConf(['api_token' => ''])));
    }

    #[Test]
    public function requestReturnsEmptyStringAndLogsErrorWhenApiUrlIsMissing(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $this->requestFactory->expects($this->never())->method('request');

        self::assertSame('', $this->createSubject($logger)->request($this->createBaseConf(['api_url' => ''])));
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string}>
     */
    public static function apiMethodProvider(): array
    {
        return [
            'public timeline' => [['api_method' => 'public_timeline'], 'timelines/public'],
            'home timeline' => [['api_method' => 'home_timeline'], 'timelines/home'],
            'list timeline' => [['api_method' => 'list_timeline', 'list_id' => '42'], 'timelines/list/42'],
            'accounts' => [['api_method' => 'accounts', 'account_id' => '99'], 'accounts/99/statuses'],
            'hashtag timeline' => [['api_method' => 'hashtag_timeline', 'hashtag' => 'typo3'], 'timelines/tag/typo3'],
        ];
    }

    #[Test]
    #[DataProvider('apiMethodProvider')]
    public function requestBuildsUrlForApiMethod(array $confOverrides, string $expectedPathFragment): void
    {
        $this->requestFactory->expects($this->once())
            ->method('request')
            ->with(self::stringContains($expectedPathFragment), 'GET', self::anything())
            ->willReturn($this->createSuccessResponse(''));

        $this->createSubject()->request($this->createBaseConf($confOverrides));
    }

    #[Test]
    public function requestSendsApiTokenAsBearerAuthorizationHeader(): void
    {
        $this->requestFactory->expects($this->once())
            ->method('request')
            ->with(self::anything(), 'GET', ['headers' => ['Authorization' => 'Bearer some-token']])
            ->willReturn($this->createSuccessResponse(''));

        $this->createSubject()->request($this->createBaseConf());
    }

    #[Test]
    public function requestAppendsQueryParametersForActiveFiltersOnly(): void
    {
        $this->requestFactory->expects($this->once())
            ->method('request')
            ->with(
                self::logicalAnd(
                    self::stringContains('only_media=1'),
                    self::stringContains('exclude_replies=1'),
                    self::logicalNot(self::stringContains('exclude_reblogs=1')),
                    self::stringContains('pinned=1'),
                ),
                'GET',
                self::anything(),
            )
            ->willReturn($this->createSuccessResponse(''));

        $this->createSubject()->request($this->createBaseConf([
            'only_media' => true,
            'exclude_replies' => true,
            'exclude_reblogs' => false,
            'only_pinned' => true,
        ]));
    }

    #[Test]
    public function requestReturnsResponseBodyOnSuccess(): void
    {
        $this->requestFactory->expects($this->once())
            ->method('request')
            ->willReturn($this->createSuccessResponse('{"foo":"bar"}'));

        self::assertSame('{"foo":"bar"}', $this->createSubject()->request($this->createBaseConf()));
    }

    #[Test]
    public function requestThrowsRuntimeExceptionAndLogsErrorWhenResponseIsNotSuccessful(): void
    {
        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);

        $this->requestFactory->expects($this->once())->method('request')->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $this->expectException(\RuntimeException::class);

        $this->createSubject($logger)->request($this->createBaseConf());
    }

    private function createSuccessResponse(string $body): ResponseInterface
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }
}
