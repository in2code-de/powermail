<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Solution for {outer.{inner}} problem with variables in fluid
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class VariableInVariableViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Solution for {outer.{inner}} problem with variables in fluid
	 *
	 * @param object $obj
	 * @param string $prop
	 * @return string
	 */
	public function render($obj, $prop) {
		if (is_object($obj) && method_exists($obj, 'get' . GeneralUtility::underscoredToUpperCamelCase($prop))) {
			return $obj->{'get' . GeneralUtility::underscoredToUpperCamelCase($prop)}();
		} elseif (is_array($obj)) {
			if (array_key_exists($prop, $obj)) {
				return $obj[$prop];
			}
		}
		return NULL;
	}
}