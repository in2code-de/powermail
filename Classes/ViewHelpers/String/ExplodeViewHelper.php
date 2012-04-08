<?php

/**
 * View helper to explode a list
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_ExplodeViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

    /**
     * View helper to explode a list
     *
     * @param 	string 		Any list (e.g. "a,b,c,d")
     * @param 	string 		Separator sign (e.g. ",")
     * @param 	boolean 	Should be trimmed?
     * @return 	array
     */
    public function render($string = '', $separator = ',', $trim = 1) {
		return $trim ? t3lib_div::trimExplode($separator, $string, 1) : explode($separator, $string);
    }
}

?>