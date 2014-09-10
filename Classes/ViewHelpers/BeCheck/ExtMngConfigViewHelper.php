<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

/**
 * Backend Check Viewhelper: Check if Extension Manager Settings are available
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class ExtMngConfigViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Check if Extension Manager Settings are available
	 *
	 * @return bool
	 */
	public function render() {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if (is_array($confArr) && count($confArr) > 2) {
			return TRUE;
		}
		return FALSE;
	}
}