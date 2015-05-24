<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Configuration;

/**
 * Class IsMarketingInformationActiveViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsMarketingInformationActiveViewHelper extends AbstractViewHelper {

	/**
	 * Check if marketing information should be shown
	 *
	 * @param array $marketingInformation
	 * @param array $settings TypoScript Configuration
	 * @return bool
	 */
	public function render($marketingInformation, $settings) {
		if (
			!empty($marketingInformation) &&
			!empty($settings['marketing']['information']) &&
			!Configuration::isDisableMarketingInformationActive()
		) {
			return TRUE;
		}
		return FALSE;
	}
}