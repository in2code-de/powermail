<?php

/**
 * View helper check if given value is empty (also empty arrays)
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsNotEmptyViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * View helper check if given value is number or not
	 *
	 * @param mixed $val String or Number
	 * @return boolean
	 */
	public function render($val) {
		if (!is_array($val)) {
			return !empty($val);
		} else {
			foreach ((array) $val as $subValue) {
				if (!empty($subValue)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}