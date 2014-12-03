<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
			$this->activeValue($settings['marketing']['information']) &&
			!$this->activeValue($settings['global']['disableMarketingInformation'])
		) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	protected function activeValue($value) {
		if (isset($value) && $value === '1') {
			return TRUE;
		}
		return FALSE;
	}
}