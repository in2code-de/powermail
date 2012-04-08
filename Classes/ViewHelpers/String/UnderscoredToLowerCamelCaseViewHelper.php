<?php

/**
 * Underscored value to lower camel case value
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_UnderscoredToLowerCamelCaseViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * Underscored value to lower camel case value (nice_field => niceField)
     *
     * @param 	string 		String
     * @return 	string
     */
    public function render($val = '') {
		return t3lib_div::underscoredToLowerCamelCase($val);
    }
}

?>