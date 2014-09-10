<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Is {outer.{inner}} a datetime?
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsDateTimeVariableInVariableViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Is {outer.{inner}} a datetime?
	 *
	 * @param object $obj
	 * @param string $prop Property
	 * @return bool
	 */
	public function render($obj, $prop) {
		$mixed = NULL;
		if (is_object($obj) && method_exists($obj, 'get' . GeneralUtility::underscoredToUpperCamelCase($prop))) {
			$mixed = $obj->{'get' . GeneralUtility::underscoredToUpperCamelCase($prop)}();
		}
		return method_exists($mixed, 'getTimestamp');
	}
}