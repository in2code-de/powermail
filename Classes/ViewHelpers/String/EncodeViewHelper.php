<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper encoding of URL for RSS Feeds
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class EncodeViewHelper extends AbstractViewHelper {

	/**
	 * Returns encoded string
	 *
	 * @return string
	 */
	public function render() {
		return htmlspecialchars($this->renderChildren());
	}
}