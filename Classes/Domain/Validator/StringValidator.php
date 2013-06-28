<?php
class Tx_Powermail_Domain_Validator_StringValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * fieldsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FieldsRepository
	 */
	protected $fieldsRepository;

	/**
	 * regEx and filter array
	 * Note: PHP filters see http://php.net/manual/en/filter.filters.sanitize.php and http://de.php.net/manual/de/function.filter-var.php
	 *
	 * @var regEx
	 */
	protected $regEx = array(
		1 => FILTER_VALIDATE_EMAIL, // email
		2 => FILTER_VALIDATE_URL, // url
		3 => '/[^0-9+ .]/', // phone
		4 => FILTER_SANITIZE_NUMBER_INT, // numbers
		5 => '/[^a-zA-Z]/', // letters
	);

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = true;

	/**
	 * Validation of given Params
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {

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

				if (is_numeric($this->regEx[$field->getValidation()])) { // filter

					if (filter_var($value, $this->regEx[$field->getValidation()]) === false) { // check failed
						$this->addError('validation', $uid);
						$this->isValid = false;
					}

				} else { // regex

					if (preg_replace($this->regEx[$field->getValidation()], '', $value) != $value) { // check failed
						$this->addError('validation', $uid);
						$this->isValid = false;
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
}
?>