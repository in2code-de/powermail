<?php

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsArrayViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper check if given value is array or not
     *
     * @param 	mixed 		String or Array
     * @return 	boolean
     */
    public function render($val = '') {
		return is_array($val);
    }
}

?>