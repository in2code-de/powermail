<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use In2code\Powermail\Domain\Model\Field;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class ValidationDataAttributeViewHelper extends AbstractValidationViewHelper {

	/**
	 * Returns Data Attribute Array for JS validation with parsley.js
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \array $additionalAttributes To add further attributes
	 * @param \mixed $iteration Iterationarray for Multi Fields (Radio, Check, ...)
	 * @return \array for data attributes
	 */
	public function render(Field $field, $additionalAttributes = array(), $iteration = NULL) {
		switch ($field->getType()) {
			case 'check':
				// multiple field radiobuttons
			case 'radio':
				$this->addMandatoryAttributesForMultipleFields($additionalAttributes, $field, $iteration);
				break;
			default:
				$this->addMandatoryAttributes($additionalAttributes, $field);
		}
		$this->addValidationAttributes($additionalAttributes, $field);
		return $additionalAttributes;
	}

	/**
	 * Set different mandatory attributes for checkboxes and radiobuttons
	 *
	 * @param \array &$additionalAttributes
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \mixed $iteration
	 * @return void
	 */
	protected function addMandatoryAttributesForMultipleFields(&$additionalAttributes, $field, $iteration) {
		if ($iteration['index'] === 0) {
			if ($field->getMandatory()) {
				if ($this->isNativeValidationEnabled()) {
					$additionalAttributes['required'] = 'required';

					// remove required attribute if more checkboxes than 1
					if ($field->getType() === 'check' && $iteration['total'] > 1) {
						unset($additionalAttributes['required']);
					}
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-required'] = 'true';
					}
				}
				if ($this->isClientValidationEnabled()) {
					$additionalAttributes['data-parsley-required-message'] = LocalizationUtility::translate(
						'validationerror_mandatory',
						$this->extensionName
					);
					// overwrite error message
					$additionalAttributes['data-parsley-required-message'] = LocalizationUtility::translate(
						'validationerror_mandatory_multi',
						$this->extensionName
					);
					if ($field->getType() === 'check' && $iteration['total'] > 1) {
						$additionalAttributes['data-parsley-required'] = 'true';
					}
				}
			}

			if ($this->isClientValidationEnabled()) {
				// define where to show errors
				$additionalAttributes['data-parsley-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
				// define where to set the error class
				$additionalAttributes['data-parsley-class-handler'] = '.powermail_fieldwrap_' . $field->getUid() . ' div:first';
			}
		}

			// add multiple attribute to bundle checkboxes for parsley
		if ($field->getMandatory() && $this->isClientValidationEnabled() && $field->getType() === 'check' && $iteration['total'] > 1) {
			$additionalAttributes['data-parsley-multiple'] = $field->getMarker();
		}
	}

	/**
	 * Set different validation attributes
	 *
	 * @param \array &$additionalAttributes
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return void
	 */
	protected function addValidationAttributes(&$additionalAttributes, $field) {
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
					$additionalAttributes['data-parsley-type'] = 'email';
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
					$additionalAttributes['data-parsley-type'] = 'url';
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
					$additionalAttributes['pattern'] = $pattern;
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-pattern'] = $pattern;
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
					$additionalAttributes['data-parsley-type'] = 'integer';
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
					$additionalAttributes['pattern'] = '[A-Za-z]+';
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-pattern'] = '[a-zA-Z]+';
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
					$additionalAttributes['min'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-min'] = $field->getValidationConfiguration();
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
					$additionalAttributes['max'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-max'] = $field->getValidationConfiguration();
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
					$additionalAttributes['min'] = intval($values[0]);
					$additionalAttributes['max'] = intval($values[1]);
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-min'] = intval($values[0]);
						$additionalAttributes['data-parsley-max'] = intval($values[1]);
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
					$additionalAttributes['data-parsley-length'] = '[' . implode(', ', $values) . ']';
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
					$additionalAttributes['pattern'] = $field->getValidationConfiguration();
				} else {
					if ($this->isClientValidationEnabled()) {
						$additionalAttributes['data-parsley-pattern'] = $field->getValidationConfiguration();
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
					$additionalAttributes['data-parsley-custom' . $field->getValidation()] = $value;
				}
		}

			// set errormessage if javascript validation active
		if ($field->getValidation() && $this->isClientValidationEnabled()) {
			$additionalAttributes['data-parsley-error-message'] = LocalizationUtility::translate(
				'validationerror_validation.' . $field->getValidation(),
				$this->extensionName
			);
		}
	}
}