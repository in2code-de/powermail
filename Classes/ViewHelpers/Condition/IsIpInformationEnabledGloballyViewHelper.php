<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Configuration;

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
		return !Configuration::isDisableIpLogActive();
	}
}