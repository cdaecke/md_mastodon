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

use Mediadreams\MdMastodon\Domain\Model\Configuration;
use Mediadreams\MdMastodon\Service\SettingsService;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * Class MastodonApiRequester
 * @package Mediadreams\MdMastodon\Http
 */
final class MastodonApiRequester
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MastodonApiRequester constructor.
     * @param RequestFactory $requestFactory
     * @param SettingsService $settingsService
     * @param LoggerInterface $logger
     */
    public function __construct(RequestFactory $requestFactory, SettingsService $settingsService, LoggerInterface $logger)
    {
        $this->requestFactory = $requestFactory;
        $this->settingsService = $settingsService;
        $this->logger = $logger;
    }

    /**
     * @param Configuration $conf
     * @return string
     */
    public function request(Configuration $conf): string
    {
        $this->settings = $this->settingsService->getSettings();
        $url = $this->getApiUrl($conf);

        $apiToken = !empty($conf->getApiToken())? $conf->getApiToken():$this->settings['apiToken'];
        if (empty($apiToken)) {
            $this->logger->error('No API token provided for configuration with Uid ' . $conf->getUid());
            return '';
        }

        $additionalOptions = [
            'headers' => ['Authorization' => 'Bearer ' . $conf->getApiToken()],
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
     * @param Configuration $conf
     * @return string
     */
    private function getApiUrl(Configuration $conf): string
    {
        $url = !empty($conf->getApiUrl())? $conf->getApiUrl():$this->settings['apiUrl'];
        if (empty($url)) {
            $this->logger->error('No API url provided for configuration with Uid ' . $conf->getUid());
            return '';
        }

        $apiUrlPath = $this->getApiUrlPath($conf);
        if (empty($apiUrlPath)) {
            $this->logger->error('Could not resolve apiUrlPath for configuration with Uid ' . $conf->getUid());
            return '';
        }

        return $url . $apiUrlPath . $this->getApiParams($conf);
    }

    /**
     * Get URL path for Api call
     *
     * @param Configuration $conf
     * @return string
     */
    private function getApiUrlPath(Configuration $conf): string
    {
        switch ($conf->getApiMethod()) {
            case 'public_timeline':
                $apiUrlPath = 'timelines/public';
                break;
            case 'home_timeline':
                $apiUrlPath = 'timelines/home';
                break;
            case 'list_timeline':
                $apiUrlPath = 'timelines/list/' . $conf->getListId();
                break;
            case 'accounts':
                $apiUrlPath = 'accounts/' . $conf->getAccountId(). '/statuses';
                break;
            case 'hashtag_timeline':
                $apiUrlPath = 'timelines/tag/' . $conf->getHashtag();
                break;
            default:
                $apiUrlPath = '';
        }

        return $apiUrlPath;
    }

    /**
     * Get params for querying the api
     *
     * @param Configuration $conf
     * @return string
     */
    private function getApiParams(Configuration $conf): string
    {
        $apiParams = '?';
        $apiParams .= $conf->getOnlyMedia()? 'only_media=1&':'';
        $apiParams .= $conf->getExcludeReplies()? 'exclude_replies=1&':'';
        $apiParams .= $conf->getExcludeReblogs()? 'exclude_reblogs=1&':'';
        $apiParams .= $conf->getOnlyPinned()? 'pinned=1&':'';

        return $apiParams;
    }
}
