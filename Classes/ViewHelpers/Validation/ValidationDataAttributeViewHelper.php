<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use TYPO3\CMS\Core\Utility\GeneralUtility,
	\TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class ValidationDataAttributeViewHelper extends AbstractValidationViewHelper {

	/**
	 * @var \string
	 */
	protected $extensionName;

	/**
	 * Returns Data Attribute Array for JS validation with parsley.js
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \array $additionalAttributes To add further attributes
	 * @param \mixed $iteration Iterationarray for Multi Fields (Radio, Check, ...)
	 * @return \array for data attributes
	 */
	public function render(\In2code\Powermail\Domain\Model\Field $field, $additionalAttributes = array(), $iteration = NULL) {
		$dataArray = $additionalAttributes;
		$this->extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
		if ($this->arguments['extensionName'] !== NULL) {
			$this->extensionName = $this->arguments['extensionName'];
		}

		$this->addMandatoryAttributes($dataArray, $field, $iteration);
		$this->addValidationAttributes($dataArray, $field);
		return $dataArray;
	}

	/**
	 * Set different mandatory attributes
	 *
	 * @param \array &$dataArray
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \mixed $iteration
	 * @return void
	 */
	protected function addMandatoryAttributes(&$dataArray, $field, $iteration) {
		if ($field->getMandatory() && ($iteration === NULL || $iteration['index'] === 0)) {
			if ($this->isNativeValidationEnabled()) {
				$dataArray['required'] = 'required';

					// don't want the required attribute if more checkboxes
				if ($field->getType() == 'check' && $iteration['total'] > 1) {
					unset($dataArray['required']);
				}
			} else {
				if ($this->isClientValidationEnabled()) {
					$dataArray['data-parsley-required'] = 'true';
				}
			}
			if ($this->isClientValidationEnabled()) {
				$dataArray['data-parsley-required-message'] = LocalizationUtility::translate(
					'validationerror_mandatory',
					$this->extensionName
				);

					// type radio, checkbox
				if ($field->getType() == 'radio' || $field->getType() == 'check') {
						// define where to show errors
					$dataArray['data-parsley-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
						// define where to set the error class
					$dataArray['data-parsley-class-handler'] = '.powermail_fieldwrap_' . $field->getUid() . ' div:first';
						// overwrite error message
					$dataArray['data-parsley-required-message'] = LocalizationUtility::translate(
						'validationerror_mandatory_multi',
						$this->extensionName
					);
					if ($field->getType() == 'check' && $iteration['total'] > 1) {
						$dataArray['data-parsley-required'] = 'true';
					}
				}
			}
		}

			// add multiple attribute to bundle checkboxes for parsley
		if ($field->getMandatory() && $this->isClientValidationEnabled() && $field->getType() == 'check' && $iteration['total'] > 1) {
			$dataArray['data-parsley-multiple'] = $field->getMarker();
		}

			// Captcha
		if ($field->getType() === 'captcha') {
			if ($this->isNativeValidationEnabled()) {
				$dataArray['required'] = 'required';
			} elseif ($this->isClientValidationEnabled()) {
				$dataArray['data-parsley-required'] = 'true';
			}
			if ($this->isClientValidationEnabled()) {
				$dataArray['data-parsley-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
				$dataArray['data-parsley-class-handler'] = '.powermail_fieldwrap_' . $field->getUid() . ' > div';
				$dataArray['data-parsley-required-message'] = LocalizationUtility::translate(
					'validationerror_mandatory',
					$this->extensionName
				);
			}
		}
	}

	/**
	 * Set different validation attributes
	 *
	 * @param \array &$dataArray
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return void
	 */
	protected function addValidationAttributes(&$dataArray, $field) {
		if ($field->getType() !== 'input' && $field->getType() !== 'textarea') {
			return;
		}

		switch ($field->getValidation()) {

			/**
			 * EMAIL (+html5)
			 *
			 * html5 example: <input type="email" />
			 * javascript example: <input type="text" data-parsley-type="email" />
			 */
			case 1:
				if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
					$dataArray['data-parsley-type'] = 'email';
				}
				break;

			/**
			 * URL (+html5)
			 *
			 * html5 example: <input type="url" />
			 * javascript example: <input type="text" data-parsley-type="url" />
			 */
			case 2:
				if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
					$dataArray['data-parsley-type'] = 'url';
				}
				break;

			/**
			 * PHONE (+html5)
			 * 		0 123 456 7890
			 * 		0123 4567890
			 * 		01234567890
			 * 		+12 345 6789012
			 * 		+12 345 678 9012
			 *
			 * html5 example:
			 * 		<input type="text"
			 * 			pattern="/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/" />
			 * javascript example:
			 * 		<input ... data-parsley-pattern=
			 * 			"/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/" />
			 */
			case 3:
				$pattern = '/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/';
				if ($this->isNativeValidationEnabled()) {
					$dataArray['pattern'] = $pattern;
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-pattern'] = $pattern;
					}
				}
				break;

			/**
			 * NUMBER/INTEGER (+html5)
			 *
			 * html5 example: <input type="number" />
			 * javascript example: <input type="text" data-parsley-type="integer" />
			 */
			case 4:
				if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
					$dataArray['data-parsley-type'] = 'integer';
				}
				break;

			/**
			 * LETTERS (+html5)
			 *
			 * html5 example: <input type="text" pattern="[a-zA-Z]." />
			 * javascript example: <input type="text" data-parsley-pattern="[a-zA-Z]." />
			 */
			case 5:
				if ($this->isNativeValidationEnabled()) {
					$dataArray['pattern'] = '[A-Za-z].';
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-pattern'] = '[a-zA-Z].';
					}
				}
				break;

			/**
			 * MIN NUMBER (+html5)
			 *
			 * Note: Field validation_configuration for editors viewable
			 * html5 example: <input type="text" min="6" />
			 * javascript example: <input type="text" data-parsley-min="6" />
			 */
			case 6:
				if ($this->isNativeValidationEnabled()) {
					$dataArray['min'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-min'] = $field->getValidationConfiguration();
					}
				}
				break;

			/**
			 * MAX NUMBER (+html5)
			 *
			 * Note: Field validation_configuration for editors viewable
			 * html5 example: <input type="text" max="12" />
			 * javascript example: <input type="text" data-parsley-max="12" />
			 */
			case 7:
				if ($this->isNativeValidationEnabled()) {
					$dataArray['max'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-max'] = $field->getValidationConfiguration();
					}
				}
				break;

			/**
			 * RANGE (+html5)
			 *
			 * Note: Field validation_configuration for editors viewable
			 * html5 example: <input type="range" min="1" max="10" />
			 * javascript example:
			 * 		<input type="text" data-parsley-type="range" min="1" max="10" />
			 */
			case 8:
				$values = GeneralUtility::trimExplode(',', $field->getValidationConfiguration(), TRUE);
				if (intval($values[0]) <= 0) {
					break;
				}
				if (!isset($values[1])) {
					$values[1] = $values[0];
					$values[0] = 1;
				}
				if ($this->isNativeValidationEnabled()) {
					$dataArray['min'] = intval($values[0]);
					$dataArray['max'] = intval($values[1]);
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-min'] = intval($values[0]);
						$dataArray['data-parsley-max'] = intval($values[1]);
					}
				}
				break;

			/**
			 * LENGTH
			 *
			 * Note: Field validation_configuration for editors viewable
			 * javascript example:
			 * 		<input type="text" data-parsley-length="[6, 10]" />
			 */
			case 9:
				$values = GeneralUtility::trimExplode(',', $field->getValidationConfiguration(), TRUE);
				if (intval($values[0]) <= 0) {
					break;
				}
				if (!isset($values[1])) {
					$values[1] = intval($values[0]);
					$values[0] = 1;
				}
				if ($this->isClientValidationEnabled()) {
					$dataArray['data-parsley-length'] = '[' . implode(', ', $values) . ']';
				}
				break;

			/**
			 * PATTERN (+html5)
			 *
			 * Note: Field validation_configuration for editors viewable
			 * html5 example: <input type="text" pattern="https?://.+" />
			 * javascript example:
			 * 		<input type="text" data-parsley-pattern="https?://.+" />
			 */
			case 10:
				if ($this->isNativeValidationEnabled()) {
					$dataArray['pattern'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$dataArray['data-parsley-pattern'] = $field->getValidationConfiguration();
					}
				}
				break;

			/**
			 * Custom Validation Attribute
			 *
			 * If CustomValidation was added via Page TSConfig
			 * 		tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
			 *
			 * <input type="text" data-parsley-custom100="1" />
			 */
			default:
				if ($field->getValidation() && $this->isClientValidationEnabled()) {
					$value = 1;
					if ($field->getValidationConfiguration()) {
						$value = $field->getValidationConfiguration();
					}
					$dataArray['data-parsley-custom' . $field->getValidation()] = $value;
				}
		}

			// set errormessage if javascript validation active
		if ($field->getValidation() && $this->isClientValidationEnabled()) {
			$dataArray['data-parsley-error-message'] = LocalizationUtility::translate(
				'validationerror_validation.' . $field->getValidation(),
				$this->extensionName
			);
		}
	}
}