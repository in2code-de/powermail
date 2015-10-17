<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Abstract Validation ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
abstract class AbstractValidationViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var ContentObjectRenderer
	 */
	protected $contentObject;

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
	 * Get FlexForm array from contentObject
	 *
	 * @return array|NULL
	 */
	protected function getFlexFormArray() {
		$flexForm = GeneralUtility::xml2array($this->contentObject->data['pi_flexform'], 'data');
		return !empty($flexForm[0]) ? $flexForm[0] : NULL;
	}

	/**
	 * Set mandatory attributes
	 *
	 * @param array &$additionalAttributes
	 * @param Field $field
	 * @return void
	 */
	protected function addMandatoryAttributes(&$additionalAttributes, Field $field = NULL) {
		if ($field !== NULL && $field->getMandatory()) {
			if ($this->isNativeValidationEnabled()) {
				$additionalAttributes['required'] = 'required';
			} else {
				if ($this->isClientValidationEnabled()) {
					$additionalAttributes['data-parsley-required'] = 'true';
				}
			}
			if ($this->isClientValidationEnabled()) {
				$additionalAttributes['data-parsley-required-message'] = LocalizationUtility::translate('validationerror_mandatory');
				$additionalAttributes['data-parsley-trigger'] = 'change';
			}
		}
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function initialize() {
		$this->extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
		$this->contentObject = $this->configurationManager->getContentObject();
		if ($this->arguments['extensionName'] !== NULL) {
			$this->extensionName = $this->arguments['extensionName'];
		}
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
		);
		if (!empty($typoScriptSetup['setup'])) {
			$this->settings = $typoScriptSetup['setup'];
		}
	}
}
