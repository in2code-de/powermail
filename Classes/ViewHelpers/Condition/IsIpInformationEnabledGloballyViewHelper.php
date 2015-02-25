<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsIpInformationEnabledGloballyViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsIpInformationEnabledGloballyViewHelper extends AbstractViewHelper {

	/**
	 * Check if IP information should be shown
	 *
	 * @return bool
	 */
	public function render() {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if (isset($confArr['disableIpLog']) && $confArr['disableIpLog'] === '1') {
			return FALSE;
		}
		return TRUE;
	}
}