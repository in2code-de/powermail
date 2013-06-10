<?php

/**
 * Check if there is a string in another string
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsStringInStringViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * Check if there is a string in another string
     *
     * @param 	string 		Haystack
     * @param 	string 		Needle
     * @return 	boolean
     */
    public function render($haystack, $needle) {
		return stristr($haystack, $needle);
    }
}

?>