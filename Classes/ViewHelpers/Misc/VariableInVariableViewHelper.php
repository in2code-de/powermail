<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class VariableInVariableViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Misc
 */
class VariableInVariableViewHelper extends AbstractViewHelper {

	/**
	 * Solution for {outer.{inner}} call in fluid
	 *
	 * @param object|array $obj object or array
	 * @param string $prop property name
	 * @return mixed
	 */
	public function render($obj, $prop) {
		if (is_array($obj) && array_key_exists($prop, $obj)) {
			return $obj[$prop];
		}
		if (is_object($obj)) {
			return ObjectAccess::getProperty($obj, $prop);
		}
		return NULL;
	}
}