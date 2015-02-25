<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Div;

/**
 * Is Backend Admin?
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsAdminViewHelper extends AbstractViewHelper {

	/**
	 * Is Backend Admin?
	 *
	 * @return bool
	 */
	public function render() {
		return Div::isBackendAdmin();
	}
}