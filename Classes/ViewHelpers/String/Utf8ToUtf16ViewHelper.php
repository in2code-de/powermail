<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * change utf8 to UTF-16LE for Excel
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Utf8ToUtf16ViewHelper extends AbstractViewHelper {

	/**
	 * change utf8 to UTF-16LE for Excel
	 *
	 * @return string
	 */
	public function render() {
		$string = chr(255) . chr(254);
		$string .= mb_convert_encoding($this->renderChildren(), 'UTF-16LE', 'UTF-8');
		return $string;
	}
}