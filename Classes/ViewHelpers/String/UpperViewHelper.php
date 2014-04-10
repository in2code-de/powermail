<?php

/**
 * View helper for upper (ucfirst())
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Powermail_ViewHelpers_String_UpperViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Use PHP Function ucfirst()
	 *
	 * @param string $string Any string
	 * @return string Changed string
	 */
	public function render($string) {
		$string = ucfirst($string);

		return $string;
	}
}