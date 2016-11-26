<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class IsHonepodEnabledViewHelper
 */
class IsHonepodEnabledViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * Configuration
     *
     * @var array
     */
    protected $settings;

    /**
     * @return bool
     */
    public function render()
    {
        return ConfigurationUtility::isValidationEnabled($this->settings, HoneyPodMethod::class);
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $configuration = $typoScriptService->convertTypoScriptArrayToPlainArray($typoScriptSetup);
        $this->settings = (array)$configuration['plugin']['tx_powermail']['settings']['setup'];
    }
}
