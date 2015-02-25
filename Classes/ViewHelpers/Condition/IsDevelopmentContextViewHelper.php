<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IsDevelopmentContextViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsDevelopmentContextViewHelper extends AbstractViewHelper {

	/**
	 * Check if Development context is active
	 *
	 * @return bool
	 */
	public function render() {
		return GeneralUtility::getApplicationContext()->isDevelopment();
	}
}