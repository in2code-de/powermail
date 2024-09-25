<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ConfigurationService to get the typoscript configuration from powermail and cache it for multiple calls
 */
class ConfigurationService implements SingletonInterface
{
    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var array
     */
    protected array $configuration = [];

    /**
     * @param string $pluginName
     * @return array
     */
    public function getTypoScriptSettings(string $pluginName = 'Pi1'): array
    {
        if (empty($this->settings[$pluginName])) {
            $this->settings[$pluginName] = $this->getTypoScriptSettingsFromOverallConfiguration($pluginName);
        }
        return $this->settings[$pluginName];
    }

    /**
     * Get configuration (formally known as $this->conf in oldschool extensions)
     *
     * @param string $pluginName
     * @return array
     */
    public function getTypoScriptConfiguration(string $pluginName = 'Pi1'): array
    {
        if (empty($this->configuration[$pluginName])) {
            $this->configuration[$pluginName] = $this->getTypoScriptConfigurationFromOverallConfiguration($pluginName);
        }
        return $this->configuration[$pluginName];
    }

    /**
     * @param string $pluginName
     * @return array
     */
    protected function getTypoScriptSettingsFromOverallConfiguration(string $pluginName): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $setup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Powermail',
            $pluginName
        );

        return $setup['setup'] ?? [];
    }

    /**
     * @param string $pluginName
     * @return array
     */
    protected function getTypoScriptConfigurationFromOverallConfiguration(string $pluginName): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $configuration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            'Powermail',
            $pluginName
        );
        if (ArrayUtility::isValidPath($configuration, 'plugin./tx_powermail./settings./setup.')) {
            return (array)$configuration['plugin.']['tx_powermail.']['settings.']['setup.'];
        }
        return [];
    }
}
