<?php
namespace In2code\Powermail\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Utility\Div;
use In2code\Powermail\Domain\Model\Form;

/**
 * CaptchaValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * GNU Lesser General Public License, version 3 or later
 */
class CaptchaValidator extends AbstractValidator {

	/**
	 * @var \In2code\Powermail\Utility\CalculatingCaptcha
	 *
	 * @inject
	 */
	protected $calculatingCaptchaEngine;

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

			$this->setCaptchaArgumentFound(TRUE);
			if (!$this->validCodePreflight($answer->getValue())) {
				$this->setErrorAndMessage($answer->getField(), 'captcha');
			}

		}

		// if no captcha arguments given (maybe deleted from DOM)
		if (!$this->getCaptchaArgumentFound()) {
			$this->addError('captcha', 0);
			$this->setIsValid(FALSE);
		}

		return $this->getIsValid();

	}

	/**
	 * Check if given string is correct
	 *
	 * @param string $value
	 * @return bool
	 */
	protected function validCodePreflight($value) {
		switch (Div::getCaptchaExtensionFromSettings($this->settings)) {
			case 'captcha':
				session_start();
				$generatedCaptchaString = $_SESSION['tx_captcha_string'];
				if ($this->getClearSession()) {
					$_SESSION['tx_captcha_string'] = '';
				}
				if (!empty($value) && $generatedCaptchaString === $value) {
					return TRUE;
				}
				break;

			default:
				if ($this->calculatingCaptchaEngine->validCode($value, $this->getClearSession())) {
					return TRUE;
				}
		}

		return FALSE;
	}

	/**
	 * Checks if given form has a captcha
	 *
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @return boolean
	 */
	protected function formHasCaptcha(Form $form) {
		$form = $this->formRepository->hasCaptcha($form);
		return count($form) ? TRUE : FALSE;
	}

	/**
	 * @return boolean
	 */
	public function getClearSession() {
		return $this->clearSession;
	}

	/**
	 * @param boolean $clearSession
	 * @return void
	 */
	public function setClearSession($clearSession) {
		$this->clearSession = $clearSession;
	}

	/**
	 * @return boolean
	 */
	public function getCaptchaArgumentFound() {
		return $this->captchaArgumentFound;
	}

	/**
	 * @param boolean $captchaArgumentFound
	 * @return void
	 */
	public function setCaptchaArgumentFound($captchaArgumentFound) {
		$this->captchaArgumentFound = $captchaArgumentFound;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$pluginVariables = GeneralUtility::_GET('tx_powermail_pi1');
		// clear captcha only on create action
		$this->setClearSession(($pluginVariables['action'] === 'create' ? TRUE : FALSE));
	}
}