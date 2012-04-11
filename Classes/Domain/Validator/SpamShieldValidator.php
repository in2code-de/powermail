<?php
class Tx_Powermail_Domain_Validator_SpamShieldValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = true;

	/**
	 * Validation of given Params
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		t3lib_utility_Debug::debug($params, __FILE__ . " " . __LINE__);
		if (!$this->akismetCheck($params)) {
			$this->addError('Akismet', 'spam');
			$this->isValid = false;
		}

		return $this->isValid;
	}

	/**
	 * Akismet check
	 *
	 * @param $params array Given params
	 * @return bool
	 */
	private function akismetCheck($params) {
		return false;
	}

	// blackListCheck
	// honeypodCheck
	// linkCheck
	// nameCheck
	// sessionCheck
	// uniqueCheck
}
?>