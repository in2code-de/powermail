<?php

/**
 * Trim Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_String_TrimViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Trim Inner HTML
	 *
	 * @return 	boolean
	 */
	public function render() {
		$string = trim($this->renderChildren());
		$string = preg_replace('/\\s\\s+/', ' ', $string);
		$string = str_replace(array('"; "', '" ; "', '" ;"'), '";"', $string);
		$string = str_replace(array('<br />', '<br>', '<br/>'), "\n", $string);
		$string = str_replace(array(" \n ", "\n ", " \n"), "\n", $string);

		return $string;
	}
}