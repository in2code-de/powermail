<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * View helper check if given value is empty (also empty arrays)
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsNotEmptyViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper check if given value is number or not
	 *
	 * @param mixed $val String or Number
	 * @return boolean
	 */
	public function render($val) {
		if (!is_array($val)) {
			$result = !empty($val);
		} else {
			$result = FALSE;
			foreach ($val as $subValue) {
				if (!empty($subValue)) {
					$result = TRUE;
				}
			}
		}

		return $result;
	}
}