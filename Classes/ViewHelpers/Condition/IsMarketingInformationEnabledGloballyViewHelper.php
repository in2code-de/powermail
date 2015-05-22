<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Configuration;

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
		return !Configuration::isDisableMarketingInformationActive();
	}
}