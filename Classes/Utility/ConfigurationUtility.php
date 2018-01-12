<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility extends AbstractUtility
{

    /**
     * Check if disableIpLog is active
     *
     * @return bool
     */
    public static function isDisableIpLogActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disableIpLog'] === '1';
    }

    /**
     * Check if disableMarketingInformation is active
     *
     * @return bool
     */
    public static function isDisableMarketingInformationActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disableMarketingInformation'] === '1';
    }

    /**
     * Check if disableBackendModule is active
     *
     * @return bool
     */
    public static function isDisableBackendModuleActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disableBackendModule'] === '1';
    }

    /**
     * Check if disablePluginInformation is active
     *
     * @return bool
     */
    public static function isDisablePluginInformationActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disablePluginInformation'] === '1';
    }

    /**
     * Check if disablePluginInformationMailPreview is active
     *
     * @return bool
     */
    public static function isDisablePluginInformationMailPreviewActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disablePluginInformationMailPreview'] === '1';
    }

    /**
     * Check if enableCaching is active
     *
     * @return bool
     */
    public static function isEnableCachingActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['enableCaching'] === '1';
    }

    /**
     * Check if replaceIrreWithElementBrowser is active
     *
     * @return bool
     */
    public static function isReplaceIrreWithElementBrowserActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['replaceIrreWithElementBrowser'] === '1';
    }

    /**
     * Check if l10n_mode_merge is active
     *
     * @return bool
     */
    public static function isL10nModeMergeActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['l10n_mode_merge'] === '1';
    }

    /**
     * Get development email (only if in dev context)
     *
     * @return false|string
     * @codeCoverageIgnore
     */
    public static function getDevelopmentContextEmail()
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (GeneralUtility::getApplicationContext()->isDevelopment() &&
            GeneralUtility::validEmail($configVariables['EXT']['powermailDevelopContextEmail'])) {
            return $configVariables['EXT']['powermailDevelopContextEmail'];
        }
        return false;
    }

    /**
     * Get default mail from install tool settings
     *
     * @param string $fallback
     * @return string
     */
    public static function getDefaultMailFromAddress($fallback = null)
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (!empty($configVariables['MAIL']['defaultMailFromAddress'])) {
            return $configVariables['MAIL']['defaultMailFromAddress'];
        }
        if ($fallback !== null) {
            return $fallback;
        }
        return '';
    }

    /**
     * Get default mail-name from install tool settings
     *
     * @return string
     */
    public static function getDefaultMailFromName()
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (!empty($configVariables['MAIL']['defaultMailFromName'])) {
            return $configVariables['MAIL']['defaultMailFromName'];
        }
        return '';
    }

    /**
     * Get path to an icon for TCA configuration
     *
     * @param string $fileName
     * @return string
     */
    public static function getIconPath($fileName)
    {
        return 'EXT:powermail/Resources/Public/Icons/' . $fileName;
    }

    /**
     * Check if a given validation is turned on generally
     * and if there is a given spamshield method enabled
     *
     * @param array $settings
     * @param string $className
     * @return bool
     */
    public static function isValidationEnabled(array $settings, $className)
    {
        $validationActivated = false;
        foreach ((array)$settings['spamshield']['methods'] as $method) {
            if ($method['class'] === $className && $method['_enable'] === '1') {
                $validationActivated = true;
                break;
            }
        }
        return !empty($settings['spamshield']['_enable']) && $validationActivated;
    }

    /**
     * Check if gdlib is loaded on this server
     *
     * @throws \Exception
     * @codeCoverageIgnore
     */
    public static function testGdExtension()
    {
        if (!extension_loaded('gd')) {
            throw new \InvalidArgumentException('PHP extension gd not loaded.', 1514819369374);
        }
    }

    /**
     * Merges Flexform, TypoScript and Extension Manager Settings
     * Note: If FF value is empty, we want the TypoScript value instead
     *
     * @param array $settings All settings
     * @param string $typoScriptLevel Startpoint
     * @return void
     */
    public static function mergeTypoScript2FlexForm(&$settings, $typoScriptLevel = 'setup')
    {
        $settings = ArrayUtility::mergeRecursiveOverrule(
            (array)$settings[$typoScriptLevel],
            (array)$settings['flexform'],
            false,
            false
        );
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     * @codeCoverageIgnore
     */
    public static function isDatabaseConnectionAvailable(): bool
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
    }
}
