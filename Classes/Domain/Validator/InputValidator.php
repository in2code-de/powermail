<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * Class InputValidator
 */
class InputValidator extends StringValidator
{
    /**
     * @var array
     */
    protected array $mandatoryValidationFieldTypes = [
        'input',
        'textarea',
        'radio',
        'check',
        'select',
        'country',
        'password',
        'file',
        'date',
    ];

    /**
     * @var array
     */
    protected array $stringValidationFieldTypes = [
        'input',
        'textarea',
    ];

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function validate($mail): Result
    {
        $this->result = new Result();
        // execute validation if it's turned on
        if ($this->isServerValidationEnabled() === true) {
            $this->isValid($mail);
        }
        return $this->result;
    }

    /**
     * Validation of given Params
     *
     * @param Mail $mail
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function isValid($mail): void
    {
        // iterate through all fields of current form
        foreach ($mail->getForm()->getPages() as $page) {
            foreach ($page->getFields() as $field) {
                $answer = $this->getAnswerFromField($field, $mail);
                $this->isValidFieldInMandatoryValidation($field, $answer);
                $this->isValidFieldInStringValidation($field, $answer);
            }
        }
    }

    /**
     * Get Answer from given field out of Mail object
     *
     * @param Field $field
     * @param Mail $mail
     * @return string|array
     */
    protected function getAnswerFromField(Field $field, Mail $mail)
    {
        foreach ($mail->getAnswers() as $answer) {
            /** @var Answer $answer */
            if ($answer->getField()->getUid() === $field->getUid()) {
                return $answer->getValue();
            }
        }
        return '';
    }

    /**
     * Validate a single field for mandatory validation
     *
     * @param Field $field
     * @param mixed $value
     * @return void
     */
    protected function isValidFieldInMandatoryValidation(Field $field, $value): void
    {
        // Mandatory Check
        if (in_array($field->getType(), $this->mandatoryValidationFieldTypes) && $field->isMandatory()) {
            if (!$this->validateMandatory($value)) {
                $this->setErrorAndMessage($field, 'mandatory');
            }
        }
    }

    /**
     * Validate a single field for any string validation
     *
     * @param Field $field
     * @param mixed $value
     * @return void
     */
    protected function isValidFieldInStringValidation(Field $field, $value): void
    {
        if (!empty($value) && in_array($field->getType(), $this->stringValidationFieldTypes)) {
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
                     *        tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
                     *
                     * Register your Class and Method with TypoScript Setup
                     *        plugin.tx_powermail.settings.setup.validation.customValidation.100 =
                     *            In2code\Powermailextended\Domain\Validator\ZipValidator
                     *
                     * Add method to your class
                     *        validate100($value, $validationConfiguration)
                     *
                     * Define your Errormessage with TypoScript Setup
                     *        plugin.tx_powermail._LOCAL_LANG.default.validationerror_validation.100 =
                     *            Error happens!
                     */
                default:
                    if ($field->getValidation()) {
                        $validation = $field->getValidation();
                        if (!empty($this->settings['validation']['customValidation'][$validation])) {
                            $extendedValidator = GeneralUtility::makeInstance(
                                $this->settings['validation']['customValidation'][$validation]
                            );
                            if (method_exists($extendedValidator, 'validate' . ucfirst((string)$validation))) {
                                if (!$extendedValidator->{'validate' . ucfirst((string)$validation)}(
                                    $value,
                                    $field->getValidationConfiguration()
                                )) {
                                    $this->setErrorAndMessage($field, 'validation.' . $validation);
                                }
                            }
                        }
                    }
            }
        }
    }
}
