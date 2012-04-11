<?php
class Tx_Powermail_Domain_Validator_CaptchaValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * fieldsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FieldsRepository
	 */
	protected $fieldsRepository;

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository;

	/**
	 * Captcha Session clean (only if mail is out)
	 *
	 * @var bool
	 */
	protected $clearSession;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = true;

	/**
	 * Captcha Field found
	 *
	 * @var bool
	 */
	private $captchaFound = false;

	/**
	 * Validation of given Captcha fields
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		if (!$this->formHasCaptcha()) {
			return $this->isValid;
		}

		foreach ((array) $params as $uid => $value) {
			// get current field values
			$field = $this->fieldsRepository->findByUid($uid);
			if (!method_exists($field, 'getUid')) {
				continue;
			}

			// if not a captcha field
			if ($field->getType() != 'captcha') {
				continue;
			}

			// if field wrong code given - set error
			$captcha = t3lib_div::makeInstance('Tx_Powermail_Utility_CalculatingCaptcha');
			if (!$captcha->validCode($value, $this->clearSession)) {
				$this->addError('captcha', $uid);
				$this->isValid = false;
			}

			// Captcha field found
			$this->captchaFound = true;
		}

		if ($this->captchaFound) {
			return $this->isValid;
		} else {
			// if no captcha vars given
			$this->addError('captcha', 0);
			return false;
		}
  	}

	/**
	 * Checks if given form has a captcha
	 */
	private function formHasCaptcha() {
		$gp = t3lib_div::_GP('tx_powermail_pi1');
		$formUid = $gp['form'];
		$form = $this->formsRepository->hasCaptcha($formUid);
		return count($form);
	}

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct() {
		$piVars = t3lib_div::_GP('tx_powermail_pi1');
		$this->clearSession = ($piVars['__referrer']['actionName'] == 'confirmation' ? true : false);
	}

	/**
	 * injectFieldsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FieldsRepository $fieldsRepository
	 * @return void
	 */
	public function injectFieldsRepository(Tx_Powermail_Domain_Repository_FieldsRepository $fieldsRepository) {
		$this->fieldsRepository = $fieldsRepository;
	}

	/**
	 * injectFormsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FormsRepository $formsRepository
	 * @return void
	 */
	public function injectFormsRepository(Tx_Powermail_Domain_Repository_FormsRepository $formsRepository) {
		$this->formsRepository = $formsRepository;
	}
}
?>