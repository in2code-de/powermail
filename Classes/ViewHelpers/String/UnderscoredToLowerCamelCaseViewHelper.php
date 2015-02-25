<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Underscored value to lower camel case value
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class UnderscoredToLowerCamelCaseViewHelper extends AbstractViewHelper {

	/**
	 * Underscored value to lower camel case value (nice_field => niceField)
	 *
	 * @param string $val
	 * @return string
	 */
	public function render($val = '') {
		return GeneralUtility::underscoredToLowerCamelCase($val);
	}
}