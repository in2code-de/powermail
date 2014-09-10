<?php
namespace In2code\Powermail\ViewHelpers\String;

/**
 * View helper for upper (ucfirst())
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class UpperViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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