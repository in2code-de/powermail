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
	 * @var validation
	 */
	protected $validationArray = array(
		1 => 'email', // email
		2 => 'url', // url
		3 => 'phone', // phone
		4 => 'integer', // numbers
		5 => 'onlyLetterSp', // letters
	);

    /**
     * Returns CSS Class for JS validation
	 * e.g. validate[required,custom[email]]
	 * http://www.position-relative.net/creation/formValidator/demos/demoValidators.html
	 * http://www.position-absolute.com/articles/jquery-form-validator-because-form-validation-is-a-mess/
     *
     * @param 	object		Current field
     * @return 	string		CSS Class
     */
    public function render($field) {
		if (!$field->getMandatory() && !$field->getValidation()) {
			return '';
		}

		$cssString = '';

		$cssString .= 'validate[';

		if ($field->getMandatory()) {
			if ($field->getType() != 'check') { // normal fields
				$cssString .= 'required';
				if ($field->getValidation()) {
					$cssString .= ',';
				}
			} else { // checkbox
//				$cssString .= 'minCheckbox[1]';
				$cssString .= 'funcCall[checkCheckboxes]';
			}
		}

		if ($field->getValidation()) {
			$cssString .= 'custom['. $this->validationArray[$field->getValidation()] . ']';
		}

		$cssString .= ']';

		return $cssString;
	}

}
?>