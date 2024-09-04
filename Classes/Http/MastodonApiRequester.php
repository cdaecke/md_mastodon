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
 * @package Mediadreams\MdMastodon\Http
 */
final class MastodonApiRequester
{
    /**
     * @var RequestFactory
     */
    protected RequestFactory $requestFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * MastodonApiRequester constructor.
     * @param RequestFactory $requestFactory
     * @param LoggerInterface $logger
     */
    public function __construct(RequestFactory $requestFactory, LoggerInterface $logger)
    {
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
    }

    /**
     * @param array $conf
     * @return string
     */
    public function request(array $conf): string
    {
        $url = $this->getApiUrl($conf);

        if (empty($conf['api_token'])) {
            $this->logger->error('No API token provided for configuration with Uid ' . $conf['uid']);
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
            $this->logger->error('Mastodon API call failed.', [
                'url' => $url,
                'additionalOptions' => $additionalOptions,
                'statusCode' => $response->getStatusCode()
            ]);

            throw new \RuntimeException('Returned status code is ' . $response->getStatusCode());
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
        switch ($conf['api_method']) {
            case 'public_timeline':
                $apiUrlPath = 'timelines/public';
                break;
            case 'home_timeline':
                $apiUrlPath = 'timelines/home';
                break;
            case 'list_timeline':
                $apiUrlPath = 'timelines/list/' . $conf['list_id'];
                break;
            case 'accounts':
                $apiUrlPath = 'accounts/' . $conf['account_id'] . '/statuses';
                break;
            case 'hashtag_timeline':
                $apiUrlPath = 'timelines/tag/' . $conf['hashtag'];
                break;
            default:
                $apiUrlPath = '';
        }

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
        $apiParams .= $conf['only_media']? 'only_media=1&':'';
        $apiParams .= $conf['exclude_replies']? 'exclude_replies=1&':'';
        $apiParams .= $conf['exclude_reblogs']? 'exclude_reblogs=1&':'';
        $apiParams .= $conf['only_pinned']? 'pinned=1&':'';

        return $apiParams;
    }
}
