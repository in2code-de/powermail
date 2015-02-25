<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Check if there is a string in another string
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsStringInStringViewHelper extends AbstractViewHelper {

	/**
	 * Check if there is a string in another string
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public function render($haystack, $needle) {
		return stristr($haystack, $needle);
	}
}