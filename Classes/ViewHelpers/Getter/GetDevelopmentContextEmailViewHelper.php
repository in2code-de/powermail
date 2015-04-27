<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\Div;

/**
 * Class GetDevelopmentContextEmailViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Getter
 */
class GetDevelopmentContextEmailViewHelper extends AbstractViewHelper {

	/**
	 * Get developmentcontext email
	 *
	 * @return  false|string
	 */
	public function render() {
		return Div::getDevelopmentContextEmail();
	}
}