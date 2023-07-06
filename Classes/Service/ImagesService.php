<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\Service;

/**
 * This file is part of the "Mastodon social networking API" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *
 * (c) 2023 Christoph Daecke <typo3@mediadreams.org>
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Log\Logger;
use \TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get all images for Mastoton feed and store them in TYPO3
 *
 * Class ImagesService
 * @package Mediadreams\MdMastodon\Service
 */
class ImagesService
{
    /**
     * @var string
     */
    protected $imageFolder = 'typo3temp/assets/tx_mdmastodon/';

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Iterate over entries and get images
     *
     * @param string $data Result of Mastodon API
     * @return string Modified result of Mastodon API, `local_image_file` was added
     */
    public function loadImages(string $data): string
    {
        $data = json_decode($data, true);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->logger = $logManager->getLogger(self::class);

        $path = GeneralUtility::getFileAbsFileName($this->imageFolder);
        $this->createFolderIfNotExists($path);

        for ($i = 0; $i < count($data); $i++) {
            $imageUrl = false;

            if (is_array($data[$i]['media_attachments']) && count($data[$i]['media_attachments']) > 0) {
                $imageUrl = $data[$i]['media_attachments'][0]['url'];
            } elseif (is_array($data[$i]['card']) && $data[$i]['card']['image']) {
                $imageUrl = $data[$i]['card']['image'];
            }

            if ($imageUrl !== false) {
                $imageExt = strrchr($imageUrl, '.');
                $fileName = $data[$i]['id'] . $imageExt;
                $pathAndName = GeneralUtility::getFileAbsFileName($this->imageFolder) . $fileName;

                // Set filename for item in database
                $data[$i]['local_image_file'] = $this->imageFolder . $fileName;

                if (!@is_file($pathAndName)) {
                    $imageContent = $this->getImageContent($imageUrl);
                    $imageSaved = GeneralUtility::writeFile($pathAndName, $imageContent, true);

                    if ($imageSaved) {
                        // If file is not an image, remove it!
                        if (getimagesize($pathAndName) === false) {
                            unlink($pathAndName);
                            $data[$i]['local_image_file'] = '';
                        }
                    } else {
                        $this->logger->error(
                            'Image could not be saved.',
                            [
                                'itemId' => $data[$i]['id'],
                                'url' => $imageUrl
                            ]
                        );
                    }
                }
            }
        }

        return json_encode($data);
    }

    /**
     * Load and return remote image
     *
     * @param string $url
     * @return string
     * @throws \RuntimeException
     */
    protected function getImageContent(string $url): string
    {
        try {
            $response = $this->requestFactory->request($url);
            if ($response->getStatusCode() === 200) {
                $content = $response->getBody()->getContents();
            } else {
                $this->logger->error('Error while loading image. Status code != 200', ['url' => $url]);
                throw new \RuntimeException('Image could not be fetched from ' . $url, 1688105672);
            }
        } catch (\Exception $exception) {
            $this->logger->error(
                'Request for image failed',
                ['url' => $url, 'exception' => $exception->getMessage()]
            );
            throw new \RuntimeException($exception->getMessage(), 1688105673);
        }

        return $content;
    }

    /**
     * Create empty folder, if not exists
     *
     * @param string $path absolute path
     * @return void
     */
    protected function createFolderIfNotExists(string $path)
    {
        if (!is_dir($path)) {
            try {
                GeneralUtility::mkdir_deep($path);
            } catch (\Exception $exception) {
                $this->logger->error('Folder could not be created.', ['Folder' => $path]);
                throw new \UnexpectedValueException('Folder ' . $path . ' could not be created', 1688103542);
            }
        }
    }
}
