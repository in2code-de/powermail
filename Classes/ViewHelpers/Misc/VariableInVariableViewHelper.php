<?php

/**
 * Solution for {outer.{inner}} problem with variables in fluid
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Misc_VariableInVariableViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Solution for {outer.{inner}} problem with variables in fluid
	 *
	 * @param $obj  object Object
	 * @param $prop	string Property
	 */
	public function render($obj, $prop) {
		if (is_object($obj) && method_exists($obj, 'get' . t3lib_div::underscoredToUpperCamelCase($prop))) {
			return $obj->{'get' . t3lib_div::underscoredToUpperCamelCase($prop)}();
		} elseif (is_array($obj)) {
			if (array_key_exists($prop, $obj)) {
				return $obj[$prop];
			}
		}
		return NULL;
	}
}

?>