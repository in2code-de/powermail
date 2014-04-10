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
	 * @param string $string Any list (e.g. "a,b,c,d")
	 * @param string $separator Separator sign (e.g. ",")
	 * @param boolean $trim Should be trimmed?
	 * @return array
	 */
	public function render($string = '', $separator = ',', $trim = TRUE) {
		return t3lib_div::trimExplode($separator, $string, $trim);
	}
}