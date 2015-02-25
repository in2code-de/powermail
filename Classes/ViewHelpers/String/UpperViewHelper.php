<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for upper (ucfirst())
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class UpperViewHelper extends AbstractViewHelper {

	/**
	 * Use PHP Function ucfirst()
	 *
	 * @param string $string Any string
	 * @return string Changed string
	 */
	public function render($string) {
		$string = ucfirst($string);

		return $string;
	}
}