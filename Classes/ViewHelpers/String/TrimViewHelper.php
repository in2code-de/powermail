<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Trim Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class TrimViewHelper extends AbstractViewHelper {

	/**
	 * Trim Inner HTML
	 *
	 * @return bool
	 */
	public function render() {
		// todo preg_match_all('/("[^"]+")/', $string, $result);
		$string = trim($this->renderChildren());
		$string = preg_replace('/\\s\\s+/', ' ', $string);
		$string = str_replace(array('"; "', '" ; "', '" ;"'), '";"', $string);
		$string = str_replace(array('<br />', '<br>', '<br/>'), "\n", $string);
		$string = str_replace(array(" \n ", "\n ", " \n"), "\n", $string);

		return $string;
	}
}