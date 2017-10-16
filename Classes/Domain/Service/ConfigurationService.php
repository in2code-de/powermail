<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * Class ConfigurationService to get the typoscript configuration from powermail
 */
class ConfigurationService implements SingletonInterface
{

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param string $pluginName
     * @return array
     */
    public function getTypoScriptSettings($pluginName = 'Pi1')
    {
        if (empty($this->settings[$pluginName])) {
            $this->settings[$pluginName] = $this->getTypoScriptSettingsFromOverallConfiguration($pluginName);
        }
        return $this->settings[$pluginName];
    }

    /**
     * @param string $pluginName
     * @return array
     */
    protected function getTypoScriptSettingsFromOverallConfiguration($pluginName)
    {
        $configurationManager = ObjectUtility::getObjectManager()->get(ConfigurationManagerInterface::class);
        $setup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Powermail',
            $pluginName
        );
        return (array)$setup['setup'];
    }
}
