<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Is {outer.{inner}} a datetime?
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsDateTimeVariableInVariableViewHelper extends AbstractViewHelper {

	/**
	 * Is {outer.{inner}} a datetime?
	 *
	 * @param object $obj
	 * @param string $prop Property
	 * @return bool
	 */
	public function render($obj, $prop) {
		return is_a(ObjectAccess::getProperty($obj, $prop), '\DateTime');
	}
}