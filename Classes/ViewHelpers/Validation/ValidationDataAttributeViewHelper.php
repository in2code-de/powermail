<?php
declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ValidationDataAttributeViewHelper
 */
class ValidationDataAttributeViewHelper extends AbstractValidationViewHelper
{
    use SignalTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('additionalAttributes', 'array', 'additionalAttributes', false, []);
        $this->registerArgument('iteration', 'mixed', 'Iterationarray for Multi Fields (Radio, Check)', false, null);
    }

    /**
     * Returns Data Attribute Array for JS validation with parsley.js
     *
     * @return array for data attributes
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $additionalAttributes = $this->arguments['additionalAttributes'];
        $iteration = $this->arguments['iteration'];
        switch ($field->getType()) {
            case 'check':
                // multiple field radiobuttons
            case 'radio':
                $this->addMandatoryAttributesForMultipleFields($additionalAttributes, $field, $iteration);
                break;
            default:
                $this->addMandatoryAttributes($additionalAttributes, $field);
        }
        $this->addValidationAttributesForInputOrTextarea($additionalAttributes, $field);
        $signalArguments = [&$additionalAttributes, $field, $iteration, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $additionalAttributes;
    }

    /**
     * Set different mandatory attributes for checkboxes and radiobuttons
     *
     * @param array &$additionalAttributes
     * @param Field $field
     * @param mixed $iteration
     * @return void
     */
    protected function addMandatoryAttributesForMultipleFields(array &$additionalAttributes, Field $field, $iteration)
    {
        if ($iteration['index'] === 0) {
            if ($field->isMandatory()) {
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['required'] = 'required';
                    $additionalAttributes['aria-required'] = 'true';

                    // remove required attribute if more checkboxes than 1
                    if ($field->getType() === 'check' && $iteration['total'] > 1) {
                        unset($additionalAttributes['required']);
                        unset($additionalAttributes['aria-required']);
                    }
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-required'] = 'true';
                        $additionalAttributes['aria-required'] = 'true';
                    }
                }
                if ($this->isClientValidationEnabled()) {
                    $additionalAttributes['data-parsley-required-message'] =
                        LocalizationUtility::translate('validationerror_mandatory');
                    if ($iteration['total'] > 1) {
                        $additionalAttributes['data-parsley-required-message'] =
                            LocalizationUtility::translate('validationerror_mandatory_multi');
                        if ($field->getType() === 'check') {
                            $additionalAttributes['data-parsley-required'] = 'true';
                            $additionalAttributes['aria-required'] = 'true';
                        }
                    }
                }
            }
            $additionalAttributes = $this->addErrorContainerAndClassHandlerAttributes($additionalAttributes, $field);
        }
        $additionalAttributes = $this->addMultipleDataAttributeForCheckboxes($additionalAttributes, $field, $iteration);
    }

    /**
     * Add validation attributes for input or textarea field types.
     *
     * @param array &$additionalAttributes
     * @param Field $field
     * @return void
     */
    protected function addValidationAttributesForInputOrTextarea(array &$additionalAttributes, Field $field)
    {
        if ($field->getType() === 'input' || $field->getType() === 'textarea') {
            $this->addValidationAttributes($additionalAttributes, $field);
        }
    }


    /**
     * Add validation attributes.
     *
     * @param array &$additionalAttributes
     * @param Field $field
     * @return void
     */
    protected function addValidationAttributes(array &$additionalAttributes, Field $field)
    {
        switch ($field->getValidation()) {

            /**
             * EMAIL (+html5)
             *
             * html5 example: <input type="email" />
             * javascript example: <input type="text" data-parsley-type="email" />
             */
            case 1:
                if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
                    $additionalAttributes['data-parsley-type'] = 'email';
                }
                break;

            /**
             * URL (+html5)
             *
             * html5 example: <input type="url" />
             * javascript example: <input type="text" data-parsley-type="url" />
             */
            case 2:
                if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
                    $additionalAttributes['data-parsley-type'] = 'url';
                }
                break;

            /**
             * PHONE (+html5)
             *        01234567890
             *        0123 4567890
             *        0123 456 789
             *        (0123) 45678 - 90
             *        0012 345 678 9012
             *        0012 (0)345 / 67890 - 12
             *        +123456789012
             *        +12 345 678 9012
             *        +12 3456 7890123
             *        +49 (0) 123 3456789
             *        +49 (0)123 / 34567 - 89
             *
             * html5 example:
             *        <input type="text"
             *            pattern="/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/" />
             * javascript example:
             *        <input ... data-parsley-pattern=
             *            "/^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$/" />
             */
            case 3:
                $pattern = '^(\+\d{1,4}|0+\d{1,5}|\(\d{1,5})[\d\s\/\(\)-]*\d+$';
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['pattern'] = $pattern;
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-pattern'] = $pattern;
                    }
                }
                break;

            /**
             * NUMBER/INTEGER (+html5)
             *
             * html5 example: <input type="number" />
             * javascript example: <input type="text" data-parsley-type="integer" />
             */
            case 4:
                if ($this->isClientValidationEnabled() && !$this->isNativeValidationEnabled()) {
                    $additionalAttributes['data-parsley-type'] = 'integer';
                }
                break;

            /**
             * LETTERS (+html5)
             *
             * html5 example: <input type="text" pattern="[a-zA-Z]." />
             * javascript example: <input type="text" data-parsley-pattern="[a-zA-Z]." />
             */
            case 5:
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['pattern'] = '[A-Za-z]+';
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-pattern'] = '[a-zA-Z]+';
                    }
                }
                break;

            /**
             * MIN NUMBER (+html5)
             *
             * Note: Field validation_configuration for editors viewable
             * html5 example: <input type="text" min="6" />
             * javascript example: <input type="text" data-parsley-min="6" />
             */
            case 6:
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['min'] = $field->getValidationConfiguration();
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-min'] = $field->getValidationConfiguration();
                    }
                }
                break;

            /**
             * MAX NUMBER (+html5)
             *
             * Note: Field validation_configuration for editors viewable
             * html5 example: <input type="text" max="12" />
             * javascript example: <input type="text" data-parsley-max="12" />
             */
            case 7:
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['max'] = $field->getValidationConfiguration();
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-max'] = $field->getValidationConfiguration();
                    }
                }
                break;

            /**
             * RANGE (+html5)
             *
             * Note: Field validation_configuration for editors viewable
             * html5 example: <input type="range" min="1" max="10" />
             * javascript example:
             *        <input type="text" data-parsley-type="range" min="1" max="10" />
             */
            case 8:
                $values = GeneralUtility::trimExplode(',', $field->getValidationConfiguration(), true);
                if ((int)$values[0] <= 0) {
                    break;
                }
                if (!isset($values[1])) {
                    $values[1] = $values[0];
                    $values[0] = 1;
                }
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['min'] = (int)$values[0];
                    $additionalAttributes['max'] = (int)$values[1];
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-min'] = (int)$values[0];
                        $additionalAttributes['data-parsley-max'] = (int)$values[1];
                    }
                }
                break;

            /**
             * LENGTH
             *
             * Note: Field validation_configuration for editors viewable
             * javascript example:
             *        <input type="text" data-parsley-length="[6, 10]" />
             */
            case 9:
                $values = GeneralUtility::trimExplode(',', $field->getValidationConfiguration(), true);
                if ((int)$values[0] <= 0) {
                    break;
                }
                if (!isset($values[1])) {
                    $values[1] = (int)$values[0];
                    $values[0] = 1;
                }
                if ($this->isClientValidationEnabled()) {
                    $additionalAttributes['data-parsley-length'] = '[' . implode(', ', $values) . ']';
                }
                break;

            /**
             * PATTERN (+html5)
             *
             * Note: Field validation_configuration for editors viewable
             * html5 example: <input type="text" pattern="https?://.+" />
             * javascript example:
             *        <input type="text" data-parsley-pattern="https?://.+" />
             */
            case 10:
                if ($this->isNativeValidationEnabled()) {
                    $additionalAttributes['pattern'] = $field->getValidationConfiguration();
                } else {
                    if ($this->isClientValidationEnabled()) {
                        $additionalAttributes['data-parsley-pattern'] = $field->getValidationConfiguration();
                    }
                }
                break;

            /**
             * Custom Validation Attribute
             *
             * If CustomValidation was added via Page TSConfig
             *        tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
             *
             * <input type="text" data-parsley-custom100="1" />
             */
            default:
                if ($field->getValidation() && $this->isClientValidationEnabled()) {
                    $value = 1;
                    if ($field->getValidationConfiguration()) {
                        $value = $field->getValidationConfiguration();
                    }
                    $additionalAttributes['data-parsley-custom' . $field->getValidation()] = $value;
                }
        }

        // set errormessage if javascript validation active
        if ($field->getValidation() && $this->isClientValidationEnabled()) {
            $additionalAttributes['data-parsley-error-message'] =
                LocalizationUtility::translate('validationerror_validation.' . $field->getValidation());
        }
    }

    /**
     * Add multiple attribute to bundle checkboxes for parsley
     *
     * @param array $additionalAttributes
     * @param Field $field
     * @param mixed $iteration
     * @return array
     */
    protected function addMultipleDataAttributeForCheckboxes(array $additionalAttributes, Field $field, $iteration)
    {
        if ($field->isMandatory() &&
            $this->isClientValidationEnabled() &&
            $field->getType() === 'check' &&
            $iteration['total'] > 1
        ) {
            $additionalAttributes['data-parsley-multiple'] = $field->getMarker();
        }
        return $additionalAttributes;
    }

    /**
     * @param array $additionalAttributes
     * @param Field $field
     * @return array
     */
    protected function addErrorContainerAndClassHandlerAttributes(array $additionalAttributes, Field $field)
    {
        if ($this->isClientValidationEnabled()) {
            $additionalAttributes = $this->addErrorContainer($additionalAttributes, $field);
            $additionalAttributes = $this->addClassHandler($additionalAttributes, $field);
        }
        return $additionalAttributes;
    }
}
