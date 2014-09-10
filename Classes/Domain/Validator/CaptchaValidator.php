<?php
namespace In2code\Powermail\Domain\Validator;

/**
 * CaptchaValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * GNU Lesser General Public License, version 3 or later
 */
class CaptchaValidator extends \In2code\Powermail\Domain\Validator\AbstractValidator {

	/**
	 * @var \In2code\Powermail\Utility\CalculatingCaptcha
	 *
	 * @inject
	 */
	protected $captchaEngine;

	/**
	 * Captcha Session clean (only if mail is out)
	 *
	 * @var bool
	 */
	protected $clearSession = TRUE;

	/**
	 * Captcha arguments found
	 *
	 * @var bool
	 */
	protected $captchaArgumentFound = FALSE;

	/**
	 * Validation of given Params
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		if (!$this->formHasCaptcha($mail->getForm())) {
			return TRUE;
		}

		foreach ($mail->getAnswers() as $answer) {
			if ($answer->getField()->getType() !== 'captcha') {
				continue;
			}

			$this->captchaArgumentFound = TRUE;
			if (!$this->captchaEngine->validCode($answer->getValue(), $this->clearSession)) {
				$this->setErrorAndMessage($answer->getField(), 'captcha');
			}

		}

			// if no captcha arguments given (maybe deleted from DOM)
		if (!$this->captchaArgumentFound) {
			$this->addError('captcha', 0);
			$this->setIsValid(FALSE);
		}

		return $this->getIsValid();

	}

	/**
	 * Checks if given form has a captcha
	 *
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @return boolean
	 */
	protected function formHasCaptcha(\In2code\Powermail\Domain\Model\Form $form) {
		$form = $this->formRepository->hasCaptcha($form);
		return count($form) ? TRUE : FALSE;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$piVars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_powermail_pi1');
			// clear captcha on create action
		$this->clearSession = ($piVars['action'] == 'create' ? TRUE : FALSE);
	}
}