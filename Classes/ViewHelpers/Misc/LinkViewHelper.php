<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class LinkViewHelper to build a link in backend context
 */
class LinkViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'like "uploads/tx_powermail/file.txt"', true);
        $this->registerArgument('absolute', 'bool', 'Want an absolute path?', false, false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $path = $this->arguments['path'];

        // Path already absolute?
        if (!is_null(parse_url($path, PHP_URL_HOST))) {
            return $path;
        }

        $uri = '';
        if ($this->arguments['absolute'] === true) {
            $uri .= parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'), PHP_URL_SCHEME);
            $uri .= '://' . GeneralUtility::getIndpEnv('HTTP_HOST') . '/';
            $uri .= trim(GeneralUtility::getIndpEnv('TYPO3_SITE_PATH'), '/');
        }

        return rtrim($uri, '/') . '/' . ltrim($path, '/');
    }
}
