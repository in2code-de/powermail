<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Get Captcha
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CaptchaViewHelper extends AbstractViewHelper
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
     * Returns Captcha-Image String
     *
     * @param Field $field
     * @return string image URL
     */
    public function render(Field $field)
    {
        switch (TypoScriptUtility::getCaptchaExtensionFromSettings($this->settings)) {
            case 'captcha':
                $captchaVersion = ExtensionManagementUtility::getExtensionVersion('captcha');
                $image = ExtensionManagementUtility::siteRelPath('captcha') . 'captcha/captcha.php';
                if (VersionNumberUtility::convertVersionNumberToInteger($captchaVersion) >= 2000000) {
                    $image = '/index.php?eID=captcha';
                }
                break;

            default:
                $captchaService = $this->objectManager->get(CalculatingCaptchaService::class);
                $image = $captchaService->render($field);
        }
        return $image;
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
        $this->settings = $configuration['plugin']['tx_powermail']['settings']['setup'];
    }
}
