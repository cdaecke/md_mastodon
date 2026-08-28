<?php

declare(strict_types=1);

namespace Mediadreams\MdMastodon\ViewHelpers;

/**
 * This file is part of the "Mastodon social networking API" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the given URL unchanged if its scheme is http or https, an empty
 * string otherwise. Used for URLs taken from remote API data (e.g. a
 * Mastodon post's permalink) that Fluid's automatic output escaping would
 * not protect against, since a scheme like `javascript:` is not HTML that
 * needs escaping - it is a dangerous value.
 */
final class SafeUrlViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'The URL to validate', true);
    }

    public function render(): string
    {
        $value = (string)$this->arguments['value'];
        $scheme = strtolower((string)parse_url($value, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $value : '';
    }
}
