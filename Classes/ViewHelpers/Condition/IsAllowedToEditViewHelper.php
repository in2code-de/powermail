<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Check if logged in User is allowed to edit
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsAllowedToEditViewHelper extends AbstractViewHelper {

	/**
	 * Check if logged in User is allowed to edit
	 *
	 * @param array $settings TypoScript and FlexForm Settings
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function render($settings = array(), $mail) {
		return FrontendUtility::isAllowedToEdit($settings, $mail);
	}

}