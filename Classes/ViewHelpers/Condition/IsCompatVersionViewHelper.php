<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Use GeneralUtitilty::compatVersion() as ViewHelper
 *
 * @package TYPO3
 * @subpackage Powermail
 */
class IsCompatVersionViewHelper extends AbstractViewHelper {

	/**
	 * Check if current TYPO3 version is greater or equal than
	 * 		given version
	 *
	 * @param string $versionNumber Minimum branch number required format x.y
	 * @return bool
	 */
	public function render($versionNumber) {
		return GeneralUtility::compat_version($versionNumber);
	}

}