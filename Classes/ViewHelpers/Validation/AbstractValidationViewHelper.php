<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Abstract Validation ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class AbstractValidationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * Configuration
	 */
	protected $settings = array();

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * Check if native validation is activated
	 *
	 * @return bool
	 */
	protected function isNativeValidationEnabled() {
		return $this->settings['validation']['native'] === '1';
	}

	/**
	 * Check if javascript validation is activated
	 *
	 * @return bool
	 */
	protected function isClientValidationEnabled() {
		return $this->settings['validation']['client'] === '1';
	}

	/**
	 * Get Current FE language
	 *
	 * @return int
	 */
	protected function getLanguageUid() {
		return $GLOBALS['TSFE']->tmpl->setup['config.']['sys_language_uid'] ?
			$GLOBALS['TSFE']->tmpl->setup['config.']['sys_language_uid'] : 0;
	}

	/**
	 * Injects the Configuration Manager
	 *
	 * @param ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		if (!empty($typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'])) {
			$this->settings = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS(
				$typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.']
			);
		}
	}
}