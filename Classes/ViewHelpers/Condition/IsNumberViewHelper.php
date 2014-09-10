<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * View helper check if given value is number or not
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsNumberViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if given value is number or not
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public function render($val = NULL) {
		return is_numeric($val);
	}
}