<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use \In2code\Powermail\Utility\Div,
	\TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper,
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility,
	\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Get Captcha
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CaptchaViewHelper extends AbstractViewHelper {

	/**
	 * PersistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * @var \In2code\Powermail\Utility\CalculatingCaptcha
	 * @inject
	 */
	protected $calculatingCaptchaEngine;

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
	 * @return string image URL
	 */
	public function render() {
		switch (Div::getCaptchaExtensionFromSettings($this->settings)) {
			case 'captcha':
				$image = ExtensionManagementUtility::siteRelPath('captcha') . 'captcha/captcha.php';
				break;

			default:
				$this->calculatingCaptchaEngine->setConfiguration($this->settings);
				$image = $this->calculatingCaptchaEngine->render();
		}
		return $image;
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function initialize() {
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}
}