<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
    public static function isDisableIpLogActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableIpLog'] === true;
    }

    /**
     * Check if disableMarketingInformation is active
     *
     * @return bool
     */
    public static function isDisableMarketingInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableMarketingInformation'] === true;
    }

    /**
     * Check if disableBackendModule is active
     *
     * @return bool
     */
    public static function isDisableBackendModuleActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableBackendModule'] === true;
    }

    /**
     * Check if disablePluginInformation is active
     *
     * @return bool
     */
    public static function isDisablePluginInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformation'] === true;
    }

    /**
     * Check if disablePluginInformationMailPreview is active
     *
     * @return bool
     */
    public static function isDisablePluginInformationMailPreviewActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformationMailPreview'] === true;
    }

    /**
     * Check if enableCaching is active
     *
     * @return bool
     */
    public static function isEnableCachingActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['enableCaching'] === true;
    }

    /**
     * Check if replaceIrreWithElementBrowser is active
     *
     * @return bool
     */
    public static function isReplaceIrreWithElementBrowserActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['replaceIrreWithElementBrowser'] === true;
    }

    /**
     * Check if l10n_mode_merge is active
     *
     * @return bool
     */
    public static function isL10nModeMergeActive()
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['l10n_mode_merge'] === true;
    }

    /**
     * @return array
     */
    public static function getExtensionConfiguration(): array
    {
        return parent::getExtensionConfiguration();
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
        $settings = ArrayUtility::arrayMergeRecursiveOverrule(
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

    /**
     * Decide if TYPO3 8.7 is used or newer
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isTypo3OlderThen9(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 9000000;
    }
}
