<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TypoScriptUtility
 */
class TypoScriptUtility
{
    /**
     * Overwrite a string if a TypoScript cObject is available
     *
     * @param string $string Value to overwrite
     * @param array|null $conf TypoScript Configuration Array
     * @param string $key Key for TypoScript Configuration
     * @codeCoverageIgnore
     */
    public static function overwriteValueFromTypoScript(
        string $string = '',
        ?array $conf = [],
        string $key = ''
    ): string {
        if (ObjectUtility::getContentObject()->cObjGetSingle($conf[$key]??'', $conf[$key . '.']??[])) {
            return ObjectUtility::getContentObject()->cObjGetSingle($conf[$key], $conf[$key . '.']);
        }

        return $string;
    }

    /**
     * Parse TypoScript from path like lib.blabla
     *
     * @codeCoverageIgnore
     */
    public static function parseTypoScriptFromTypoScriptPath(string $typoScriptObjectPath): string
    {
        if ($typoScriptObjectPath === '' || $typoScriptObjectPath === '0') {
            return '';
        }

        $request = self::getRequest();
        $setup = $request->getAttribute('frontend.typoscript')->getSetupArray();
        $pathSegments = GeneralUtility::trimExplode('.', $typoScriptObjectPath);
        $lastSegment = array_pop($pathSegments);
        foreach ($pathSegments as $segment) {
            $setup = $setup[$segment . '.'];
        }

        return ObjectUtility::getContentObject()->cObjGetSingle($setup[$lastSegment], $setup[$lastSegment . '.']);
    }

    /**
     * Return configured captcha extension
     */
    public static function getCaptchaExtensionFromSettings(array $settings): string
    {
        $allowedExtensions = [
            'captcha',
        ];
        if (in_array($settings['captcha']['use'], $allowedExtensions) &&
            ExtensionManagementUtility::isLoaded($settings['captcha']['use'])) {
            // @codeCoverageIgnoreStart
            return $settings['captcha']['use'];
            // @codeCoverageIgnoreEnd
        }

        return 'default';
    }

    private static function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
