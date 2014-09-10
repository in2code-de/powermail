<?php
namespace In2code\Powermail\ViewHelpers\String;

/**
 * View helper encoding of URL for RSS Feeds
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class EncodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns encoded string
	 *
	 * @return string
	 */
	public function render() {
		return htmlspecialchars($this->renderChildren());
	}
}