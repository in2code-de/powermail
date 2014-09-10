<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * View helper check if given value is array or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsArrayViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if given value is array or not
	 *
	 * @param mixed $val String or Array
	 * @return bool
	 */
	public function render($val = '') {
		return is_array($val);
	}
}