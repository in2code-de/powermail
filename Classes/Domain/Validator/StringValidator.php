<?php

/**
 * Class Tx_Powermail_Domain_Validator_StringValidator
 */
class Tx_Powermail_Domain_Validator_StringValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * fieldsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FieldsRepository
	 */
	protected $fieldsRepository;

	/**
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;

	/**
	 * regEx and filter array
	 * Note: PHP filters see
	 * 			http://php.net/manual/en/filter.filters.sanitize.php and
	 * 			http://de.php.net/manual/de/function.filter-var.php
	 *
	 * @var regEx
	 */
	protected $regEx = array(
		1 => FILTER_VALIDATE_EMAIL,
		2 => FILTER_VALIDATE_URL,
		3 => '/[^0-9+ .]/',
		4 => FILTER_SANITIZE_NUMBER_INT,
		5 => '/[^a-zA-Z]/'
	);

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = TRUE;

	/**
	 * Validation of given Params
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'StringValidation', array($params, $this));

		foreach ((array) $params as $uid => $value) {
			// get current field values
			$field = $this->fieldsRepository->findByUid($uid);
			if (!method_exists($field, 'getUid')) {
				continue;
			}

			// if validation of field or value empty
			if (empty($value) || !$field->getValidation()) {
				continue;
			}

			// if regex or filter found
			if (isset($this->regEx[$field->getValidation()])) {

				if (is_numeric($this->regEx[$field->getValidation()])) {

					if (filter_var($value, $this->regEx[$field->getValidation()]) === FALSE) {
						$this->addError('validation', $uid);
						$this->isValid = FALSE;
					}

				} else {

					if (preg_replace($this->regEx[$field->getValidation()], '', $value) != $value) {
						$this->addError('validation', $uid);
						$this->isValid = FALSE;
					}

				}
			}

		}

		return $this->isValid;
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
	 * @param Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher
	 * @return void
	 */
	public function injectSignalSlotDispatcher(Tx_Extbase_SignalSlot_Dispatcher $signalSlotDispatcher) {
		$this->signalSlotDispatcher = $signalSlotDispatcher;
	}
}