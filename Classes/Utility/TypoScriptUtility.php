<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TypoScriptUtility
 */
class TypoScriptUtility extends AbstractUtility
{

    /**
     * Overwrite a string if a TypoScript cObject is available
     *
     * @param string $string Value to overwrite
     * @param array $conf TypoScript Configuration Array
     * @param string $key Key for TypoScript Configuration
     * @return void
     * @codeCoverageIgnore
     */
    public static function overwriteValueFromTypoScript(&$string = null, $conf = [], $key = '')
    {
        if (self::getContentObject()->cObjGetSingle($conf[$key], $conf[$key . '.'])) {
            $string = self::getContentObject()->cObjGetSingle($conf[$key], $conf[$key . '.']);
        }
    }

    /**
     * Parse TypoScript from path like lib.blabla
     *
     * @param $typoScriptObjectPath
     * @return string
     * @codeCoverageIgnore
     */
    public static function parseTypoScriptFromTypoScriptPath($typoScriptObjectPath)
    {
        if (empty($typoScriptObjectPath)) {
            return '';
        }
        $setup = self::getTyposcriptFrontendController()->tmpl->setup;
        $pathSegments = GeneralUtility::trimExplode('.', $typoScriptObjectPath);
        $lastSegment = array_pop($pathSegments);
        foreach ($pathSegments as $segment) {
            $setup = $setup[$segment . '.'];
        }
        return self::getContentObject()->cObjGetSingle($setup[$lastSegment], $setup[$lastSegment . '.']);
    }

    /**
     * Return configured captcha extension
     *
     * @param array $settings
     * @return string
     */
    public static function getCaptchaExtensionFromSettings($settings)
    {
        $allowedExtensions = [
            'captcha'
        ];
        if (in_array($settings['captcha']['use'], $allowedExtensions) &&
            ExtensionManagementUtility::isLoaded($settings['captcha']['use'])) {
            // @codeCoverageIgnoreStart
            return $settings['captcha']['use'];
            // @codeCoverageIgnoreEnd
        }
        return 'default';
    }
}
