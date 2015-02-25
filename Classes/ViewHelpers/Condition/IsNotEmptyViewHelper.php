<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Div;

/**
 * View helper check if given value is empty (also empty arrays)
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsNotEmptyViewHelper extends AbstractViewHelper {

	/**
	 * View helper check if given value is empty
	 *
	 * @param mixed $val String or Number
	 * @return boolean
	 */
	public function render($val) {
		return Div::isNotEmpty($val);
	}
}