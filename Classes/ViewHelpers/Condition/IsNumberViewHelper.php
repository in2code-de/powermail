<?php

/**
 * View helper check if given value is number or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsNumberViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper check if given value is number or not
     *
     * @param 	mixed 		String or Number
     * @return 	boolean
     */
    public function render($val = '') {
		return is_numeric($val);
    }
}

?>