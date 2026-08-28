<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Http;

/**
 * This file is part of the "Mastodon social networking API" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Christoph Daecke <typo3@mediadreams.org>
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * Class MastodonApiRequester
 */
final readonly class MastodonApiRequester
{
    /**
     * MastodonApiRequester constructor.
     * @param RequestFactory $requestFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private RequestFactory $requestFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * @param array $conf
     * @return string
     */
    public function request(array $conf): string
    {
        if (empty($conf['api_token'])) {
            $this->logger->error('No API token provided for configuration with Uid ' . $conf['uid']);
            return '';
        }

        $url = $this->getApiUrl($conf);
        if (empty($url)) {
            // getApiUrl() already logged the specific reason.
            return '';
        }

        $additionalOptions = [
            'headers' => ['Authorization' => 'Bearer ' . $conf['api_token']],
        ];

        $response = $this->requestFactory->request(
            $url,
            'GET',
            $additionalOptions
        );

        if ($response->getStatusCode() !== 200) {
            // Deliberately not logging $additionalOptions: it carries the bearer token.
            $this->logger->error('Mastodon API call failed.', [
                'url' => $url,
                'statusCode' => $response->getStatusCode(),
            ]);

            throw new \RuntimeException('Returned status code is ' . $response->getStatusCode(), 2558427448);
        }

        return $response->getBody()->getContents();
    }

    /**
     * Get API URL
     *
     * @param array $conf
     * @return string
     */
    private function getApiUrl(array $conf): string
    {
        $url = $conf['api_url'];
        if (empty($url)) {
            $this->logger->error('No API url provided for configuration with Uid ' . $conf['uid']);
            return '';
        }

        $apiUrlPath = $this->getApiUrlPath($conf);
        if (empty($apiUrlPath)) {
            $this->logger->error('Could not resolve apiUrlPath for configuration with Uid ' . $conf['uid']);
            return '';
        }

        return $url . $apiUrlPath . $this->getApiParams($conf);
    }

    /**
     * Get URL path for Api call
     *
     * @param array $conf
     * @return string
     */
    private function getApiUrlPath(array $conf): string
    {
        $apiUrlPath = match ($conf['api_method']) {
            'public_timeline' => 'timelines/public',
            'home_timeline' => 'timelines/home',
            'list_timeline' => 'timelines/list/' . $conf['list_id'],
            'accounts' => 'accounts/' . $conf['account_id'] . '/statuses',
            'hashtag_timeline' => 'timelines/tag/' . $conf['hashtag'],
            default => '',
        };

        return $apiUrlPath;
    }

    /**
     * Get params for querying the api
     *
     * @param array $conf
     * @return string
     */
    private function getApiParams(array $conf): string
    {
        $apiParams = '?';
        $apiParams .= $conf['only_media'] ? 'only_media=1&' : '';
        $apiParams .= $conf['exclude_replies'] ? 'exclude_replies=1&' : '';
        $apiParams .= $conf['exclude_reblogs'] ? 'exclude_reblogs=1&' : '';
        $apiParams .= $conf['only_pinned'] ? 'pinned=1&' : '';

        return $apiParams;
    }
}
