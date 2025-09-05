<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Exception\SoftwareIsMissingException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ArrayUtility as CoreArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility
{
    /**
     * Check if disableIpLog is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisableIpLogActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableIpLog'];
    }

    /**
     * Check if disableMarketingInformation is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisableMarketingInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableMarketingInformation'];
    }

    /**
     * Check if disablePluginInformation is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisablePluginInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformation'];
    }

    /**
     * Check if disablePluginInformationMailPreview is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisablePluginInformationMailPreviewActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformationMailPreview'];
    }

    /**
     * Check if enableCaching is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isEnableCachingActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['enableCaching'];
    }

    /**
     * Check if replaceIrreWithElementBrowser is active
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isReplaceIrreWithElementBrowserActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['replaceIrreWithElementBrowser'];
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtensionConfiguration(): array
    {
        return (array)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('powermail');
    }

    /**
     * Get development email (only if in dev context)
     *
     * @codeCoverageIgnore
     */
    public static function getDevelopmentContextEmail(): string
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (Environment::getContext()->isDevelopment() &&
            GeneralUtility::validEmail($configVariables['EXT']['powermailDevelopContextEmail'] ?? '')) {
            return $configVariables['EXT']['powermailDevelopContextEmail'];
        }

        return '';
    }

    public static function getDefaultMailFromAddress(?string $fallback = null): string
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
     */
    public static function getDefaultMailFromName(): string
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (!empty($configVariables['MAIL']['defaultMailFromName'])) {
            return $configVariables['MAIL']['defaultMailFromName'];
        }

        return '';
    }

    /**
     * Get path to an icon for TCA configuration
     */
    public static function getIconPath(string $fileName): string
    {
        return 'EXT:powermail/Resources/Public/Icons/' . $fileName;
    }

    /**
     * Check if a given validation is turned on generally
     * and if there is a given spamshield method enabled
     */
    public static function isValidationEnabled(array $settings, string $className): bool
    {
        $validationActivated = false;
        if (CoreArrayUtility::isValidPath($settings, 'spamshield/methods')) {
            foreach ((array)$settings['spamshield']['methods'] as $method) {
                if (!empty($method['class'])
                    && !empty($method['_enable'])
                    && $method['class'] === $className
                    && $method['_enable'] === '1') {
                    $validationActivated = true;
                    break;
                }
            }
        }

        return !empty($settings['spamshield']['_enable']) && $validationActivated;
    }

    /**
     * Check if gdlib is loaded on this server
     *
     * @codeCoverageIgnore
     * @throws SoftwareIsMissingException
     */
    public static function testGdExtension(): void
    {
        if (!extension_loaded('gd')) {
            throw new SoftwareIsMissingException('PHP extension gd not loaded.', 1514819369374);
        }
    }

    /**
     * Merges Flexform, TypoScript and Extension Manager Settings
     * Note: If FF value is empty, we want the TypoScript value instead
     *
     * ToDo v14: Maybe drop this method completely and stick to TYPO3 default behavior ore use the extbase option;
     * see EXT:news -- overrideFlexFormsIfEmpty
     * see Core:Extbase -- ignoreFlexFormSettingsIfEmpty
     *
     * @param array $settings All settings
     * @param string $typoScriptLevel Startpoint
     */
    public static function mergeTypoScript2FlexForm(array $settings, string $typoScriptLevel = 'setup'): array
    {
        $originalSettings = $settings;
        if (array_key_exists($typoScriptLevel, $settings) && array_key_exists('flexform', $settings)) {
            $settings =  ArrayUtility::arrayMergeRecursiveOverrule(
                (array)$settings[$typoScriptLevel],
                (array)$settings['flexform'],
                false,
                false
            );

            // ToDo: remove for TYPO3 v14 compatible version
            // Reason for this part is, the `emptyValuesOverride = true` in the arrayMergeRecursiveOverrule from above
            $features = GeneralUtility::makeInstance(Features::class);
            if ($features->isFeatureEnabled('powermailEditorsAreAllowedToSendAttachments')) {
                if (isset($originalSettings['flexform']['receiver']['attachment'])) {
                    $settings['receiver']['attachment'] = $originalSettings['flexform']['receiver']['attachment'];
                }
                if (isset($originalSettings['flexform']['sender']['attachment'])) {
                    $settings['sender']['attachment'] = $originalSettings['flexform']['sender']['attachment'];
                }
            }
        }

        return $settings;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @codeCoverageIgnore
     */
    public static function isDatabaseConnectionAvailable(): bool
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getTypo3ConfigurationVariables(): array
    {
        return (array)$GLOBALS['TYPO3_CONF_VARS'];
    }
}
