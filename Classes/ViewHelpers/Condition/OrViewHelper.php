<?php

class Tx_Powermail_ViewHelpers_Condition_OrViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * OR viewhelper for if widget in fluid
	 *
	 * @param array $array Array with strings
	 * @param string $string String to compare (if empty, check array if values)
	 * @return boolean
	 */
	public function render($array, $string = NULL) {
		foreach ((array) $array as $value) {
			if (!$string && $value) {
				return TRUE;
			}
			if ($string && $value == $string) {
				return TRUE;
			}
		}
		return FALSE;
	}
}