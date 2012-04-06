<?php

/**
 * Is {outer.{inner}} a datetime?
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Condition_IsDateTimeVariableInVariableViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Is {outer.{inner}} a datetime?
	 *
	 * @param	$obj	object Object
	 * @param	$prop	string Property
	 * @return	bool
	 */
	public function render($obj, $prop) {
		if (is_object($obj) && method_exists($obj, 'get' . t3lib_div::underscoredToUpperCamelCase($prop))) {
			$mixed = $obj->{'get' . t3lib_div::underscoredToUpperCamelCase($prop)}();
		}
		return method_exists($mixed, 'getTimestamp');
	}
}

?>