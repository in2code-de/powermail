<?php
namespace In2code\Powermail\Domain\Validator;

use \In2code\Powermail\Domain\Model\Field;

/**
 * InputValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * GNU Lesser General Public License, version 3 or later
 */
class InputValidator extends \In2code\Powermail\Domain\Validator\StringValidator {

	/**
	 * Validation of given Params
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		// stop validation if it's turned off
		if (!$this->isServerValidationEnabled()) {
			return TRUE;
		}

		// iterate through all fields of current form
		// every page
		foreach ($mail->getForm()->getPages() as $page) {
			// every field
			foreach ($page->getFields() as $field) {
				$this->isValidField(
					$field,
					$this->getAnswerFromField($field, $mail)
				);
			}
		}

		return $this->getIsValid();
	}

	/**
	 * Get Answer from given field out of Mail object
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return \string Answer value
	 */
	protected function getAnswerFromField($field, $mail) {
		foreach ($mail->getAnswers() as $answer) {
			if ($answer->getField() === $field) {
				return $answer->getValue();
			}
		}
		return '';
	}

	/**
	 * Validate a single field
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \mixed $value
	 * @param $field
	 * @return void
	 */
	protected function isValidField(Field $field, $value) {
		// Mandatory Check
		if ($field->getMandatory()) {
			if (!$this->validateMandatory($value)) {
				$this->setErrorAndMessage($field, 'mandatory');
			}
		}

		// String Checks
		if (!empty($value)) {
			switch ($field->getValidation()) {

				// email
				case 1:
					if (!$this->validateEmail($value)) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// URL
				case 2:
					if (!$this->validateUrl($value)) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// phone
				case 3:
					if (!$this->validatePhone($value)) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// numbers only
				case 4:
					if (!$this->validateNumbersOnly($value)) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// letters only
				case 5:
					if (!$this->validateLettersOnly($value)) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// min number
				case 6:
					if (!$this->validateMinNumber($value, $field->getValidationConfiguration())) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// max number
				case 7:
					if (!$this->validateMaxNumber($value, $field->getValidationConfiguration())) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// range
				case 8:
					if (!$this->validateRange($value, $field->getValidationConfiguration())) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// length
				case 9:
					if (!$this->validateLength($value, $field->getValidationConfiguration())) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				// pattern
				case 10:
					if (!$this->validatePattern($value, $field->getValidationConfiguration())) {
						$this->setErrorAndMessage($field, 'validation.' . $field->getValidation());
					}
					break;

				/**
				 * E.g. Validation was extended with Page TSconfig
				 * 		tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
				 *
				 * Register your Class and Method with TypoScript Setup
				 * 		plugin.tx_powermail.settings.setup.validation.customValidation.100 =
				 * 			\In2code\Powermailextended\Domain\Validator\ZipValidator
				 *
				 * Add method to your class
				 * 		validate100($value, $validationConfiguration)
				 *
				 * Define your Errormessage with TypoScript Setup
				 * 		plugin.tx_powermail._LOCAL_LANG.default.validationerror_validation.100 =
				 * 			Error happens!
				 */
				default:
					if ($field->getValidation()) {
						$validation = $field->getValidation();
						if (!empty($this->settings['validation.']['customValidation.'][$validation])) {
							$extendedValidator = $this->objectManager->get($this->settings['validation.']['customValidation.'][$validation]);
							if (method_exists($extendedValidator, 'validate' . ucfirst($validation))) {
								if (!$extendedValidator->{'validate' . ucfirst($validation)}($value, $field->getValidationConfiguration())) {
									$this->setErrorAndMessage($field, 'validation.' . $validation);
								}
							}
						}
					}
			}
		}
	}
}