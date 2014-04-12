<?php
class Tx_Powermail_Domain_Validator_CaptchaValidator extends Tx_Powermail_Domain_Validator_AbstractValidator {

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
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;

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
	protected $isValid = TRUE;

	/**
	 * Captcha Field found
	 *
	 * @var bool
	 */
	protected $captchaFound = FALSE;

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
			$captcha = $this->objectManager->get('Tx_Powermail_Utility_CalculatingCaptcha');
			if (!$captcha->validCode($value, $this->clearSession)) {
				$this->addError('captcha', $uid);
				$this->isValid = FALSE;
			}

			// Captcha field found
			$this->captchaFound = TRUE;
		}

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'CaptchaValidation', array($params, $this));

		if ($this->captchaFound) {
			return $this->isValid;
		} else {
			// if no captcha vars given
			$this->addError('captcha', 0);
			return FALSE;
		}

		return FALSE;
	}

	/**
	 * Checks if given form has a captcha
	 */
	protected function formHasCaptcha() {
		$gp = t3lib_div::_GP('tx_powermail_pi1');
		$formUid = $gp['form'];
		$form = $this->formsRepository->hasCaptcha($formUid);
		return count($form);
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$piVars = t3lib_div::_GET('tx_powermail_pi1');

		// clear captcha on create action
		$this->clearSession = ($piVars['action'] == 'create' ? TRUE : FALSE);
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

	/**
	 * @param Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher
	 * @return void
	 */
	public function injectSignalSlotDispatcher(Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher) {
		$this->signalSlotDispatcher = $signalSlotDispatcher;
	}
}