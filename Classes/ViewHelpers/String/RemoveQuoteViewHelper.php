<?php
namespace In2code\Powermail\ViewHelpers\String;

/**
 * Remove Quotes from Inner HTML
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class RemoveQuoteViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Remove Quotes from Inner HTML
	 *
	 * @return 	boolean
	 */
	public function render() {
		$string = str_replace('"', '\'', $this->renderChildren());

		return $string;
	}
}