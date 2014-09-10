<?php
namespace In2code\Powermail\ViewHelpers\Validation;

/**
 * Get Captcha
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CaptchaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \In2code\Powermail\Utility\CalculatingCaptcha
	 * @inject
	 */
	protected $captchaEngine;

	/**
	 * Configuration
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Returns Captcha-Image String
	 *
	 * @return string HTML-Tag for Captcha image
	 */
	public function render() {
		$this->captchaEngine->setConfiguration($this->settings);
		return $this->captchaEngine->render();
	}

	/**
	 * Object initialization
	 *
	 * @return void
	 */
	public function initializeObject() {
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}
}