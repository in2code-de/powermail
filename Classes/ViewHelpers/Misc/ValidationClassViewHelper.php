<?php

/**
 * Returns CSS Classes for JS Validator
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_Misc_ValidationClassViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Validation array
	 *
	 * @var array
	 */
	protected $validationArray = array(
		1 => 'email',
		2 => 'url',
		3 => 'phone',
		4 => 'integer',
		5 => 'onlyLetterSp'
	);

	/**
	 * Returns CSS Class for JS validation
	 * 		e.g. validate[required,custom[email]]
	 * 		http://www.position-relative.net/
	 * 			creation/formValidator/demos/demoValidators.html
	 * 		http://www.position-absolute.com/
	 * 			articles/jquery-form-validator-because-form-validation-is-a-mess/
	 *
	 * @param object $field Current field
	 * @return string CSS Class
	 */
	public function render($field) {
		if (!$field->getMandatory() && !$field->getValidation()) {
			return '';
		}

		$cssString = '';

		$cssString .= 'validate[';

		if ($field->getMandatory()) {
			if ($field->getType() != 'check') {
				$cssString .= 'required';
				if ($field->getValidation()) {
					$cssString .= ',';
				}
			} else {
				$cssString .= 'funcCall[checkCheckboxes]';
			}
		}

		if ($field->getValidation()) {
			$cssString .= 'custom[' . $this->validationArray[$field->getValidation()] . ']';
		}

		$cssString .= ']';

		return $cssString;
	}

}