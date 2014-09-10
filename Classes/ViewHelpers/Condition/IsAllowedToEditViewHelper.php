<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * Check if logged in User is allowed to edit
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsAllowedToEditViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Div Methods
	 *
	 * @var \In2code\Powermail\Utility\Div
	 * @inject
	 */
	protected $div;

	/**
	 * Check if logged in User is allowed to edit
	 *
	 * @param array $settings TypoScript and FlexForm Settings
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function render($settings = array(), $mail) {
		return $this->div->isAllowedToEdit($settings, $mail);
	}

}