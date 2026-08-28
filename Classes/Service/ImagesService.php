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

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get all images for Mastoton feed and store them in TYPO3
 *
 * Class ImagesService
 */
class ImagesService
{
    // No svg: it can embed <script> and would be an XSS vector in its own right once served from this path.
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

    private const ALLOWED_IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];

    protected string $imageFolder = 'typo3temp/assets/tx_mdmastodon/';

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Iterate over entries and get images
     *
     * @param string $data Result of Mastodon API
     * @return string Modified result of Mastodon API, `local_image_file` was added
     */
    public function loadImages(string $data): string
    {
        $decoded = json_decode($data, true);

        if (!is_array($decoded)) {
            $this->logger->error('Could not decode Mastodon API result as an array.');
            return $data;
        }

        $data = $decoded;

        $path = GeneralUtility::getFileAbsFileName($this->imageFolder);
        $this->createFolderIfNotExists($path);

        for ($i = 0; $i < count($data); $i++) {
            if (!is_array($data[$i])) {
                continue;
            }

            $imageUrl = $this->resolveImageUrl($data[$i]);

            if ($imageUrl !== false) {
                $extension = $this->extractAllowedExtension($imageUrl);

                if ($extension === null) {
                    $this->logger->error('Image URL has no allowed file extension.', [
                        'itemId' => $data[$i]['id'],
                        'url' => $imageUrl,
                    ]);
                    $data[$i]['local_image_file'] = '';
                    continue;
                }

                // Derived from the URL rather than the remote id, so a crafted id cannot influence the path.
                $fileName = sha1($imageUrl) . '.' . $extension;
                $pathAndName = GeneralUtility::getFileAbsFileName($this->imageFolder) . $fileName;

                // Set filename for item in database
                $data[$i]['local_image_file'] = $this->imageFolder . $fileName;

                if (!@is_file($pathAndName)) {
                    $imageContent = $this->getImageContent($imageUrl);

                    if (!$this->isAllowedImageContent($imageContent)) {
                        $this->logger->error('Downloaded file is not an allowed image type.', [
                            'itemId' => $data[$i]['id'],
                            'url' => $imageUrl,
                        ]);
                        $data[$i]['local_image_file'] = '';
                        continue;
                    }

                    $error = GeneralUtility::writeFileToTypo3tempDir($pathAndName, $imageContent);

                    if ($error !== null) {
                        $this->logger->error(
                            'Image could not be saved.',
                            [
                                'itemId' => $data[$i]['id'],
                                'url' => $imageUrl,
                                'error' => $error,
                            ]
                        );
                        $data[$i]['local_image_file'] = '';
                    }
                }
            }
        }

        return json_encode($data);
    }

    /**
     * Resolve the most relevant image URL for a single Mastodon status item:
     * media attachment, card image, reblog media attachment, or reblog card
     * image (in that priority order). For videos, the preview image is used
     * instead of the video file itself.
     *
     * @param array $item Single item of the Mastodon API result
     */
    private function resolveImageUrl(array $item): string|false
    {
        if (is_array($item['media_attachments']) && count($item['media_attachments']) > 0) {
            return $item['media_attachments'][0]['type'] == 'video'
                ? $item['media_attachments'][0]['preview_url']
                : $item['media_attachments'][0]['url'];
        }

        if (is_array($item['card']) && $item['card']['image']) {
            return $item['card']['image'];
        }

        if (is_array($item['reblog']) && $item['reblog']['media_attachments']) {
            return $item['reblog']['media_attachments'][0]['type'] == 'video'
                ? $item['reblog']['media_attachments'][0]['preview_url']
                : $item['reblog']['media_attachments'][0]['url'];
        }

        if (!empty($item['reblog']['card']['image'])) {
            return $item['reblog']['card']['image'];
        }

        return false;
    }

    /**
     * Extract the file extension from an image URL's path, restricted to a
     * fixed allowlist so a remote URL cannot smuggle in an executable
     * extension (e.g. `.php`).
     */
    private function extractAllowedExtension(string $url): ?string
    {
        $path = (string)parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true) ? $extension : null;
    }

    /**
     * Verify the downloaded content is actually an allowed image type,
     * based on its content rather than its (attacker-controlled) URL.
     */
    private function isAllowedImageContent(string $content): bool
    {
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);

        return in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES, true);
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
     */
    protected function createFolderIfNotExists(string $path): void
    {
        if (!is_dir($path)) {
            try {
                GeneralUtility::mkdir_deep($path);
            } catch (\Exception) {
                $this->logger->error('Folder could not be created.', ['Folder' => $path]);
                throw new \UnexpectedValueException('Folder ' . $path . ' could not be created', 1688103542);
            }
        }
    }
}
