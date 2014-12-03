<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsMarketingInformationEnabledGloballyViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsMarketingInformationEnabledGloballyViewHelper extends AbstractViewHelper {

	/**
	 * Check if marketing information should be shown
	 *
	 * @return bool
	 */
	public function render() {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if (!isset($confArr['disableMarketingInformation']) || $confArr['disableMarketingInformation'] === '1') {
			return FALSE;
		}
		return TRUE;
	}
}