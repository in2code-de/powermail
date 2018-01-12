<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;

/**
 * Class DatepickerDataAttributeViewHelper
 */
class DatepickerDataAttributeViewHelper extends AbstractValidationViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('additionalAttributes', 'array', 'additionalAttributes', false, []);
        $this->registerArgument('value', 'string', 'Value of this field', false, '');
    }

    /**
     * Returns Data Attribute Array Datepicker settings (FE + BE)
     *
     * @return array for data attributes
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $additionalAttributes = $this->arguments['additionalAttributes'];
        $value = $this->arguments['value'];

        $additionalAttributes['data-datepicker-force'] =
            $this->settings['misc']['datepicker']['forceJavaScriptDatePicker'];
        $additionalAttributes['data-datepicker-settings'] = $this->getDatepickerSettings($field);
        $additionalAttributes['data-datepicker-months'] = $this->getMonthNames();
        $additionalAttributes['data-datepicker-days'] = $this->getDayNames();
        $additionalAttributes['data-datepicker-format'] = $this->getFormat($field);
        if ($value) {
            $additionalAttributes['data-date-value'] = $value;
        }

        $this->addMandatoryAttributes($additionalAttributes, $field);

        return $additionalAttributes;
    }

    /**
     * Get Datepicker Settings
     *
     * @param Field $field
     * @return string
     */
    protected function getDatepickerSettings(Field $field = null)
    {
        if ($field === null) {
            return 'datetime';
        }
        return $field->getDatepickerSettings();
    }

    /**
     * Get timeformat out of datepicker type
     *
     * @param Field $field
     * @return string
     */
    protected function getFormat(Field $field = null)
    {
        return LocalizationUtility::translate('datepicker_format_' . $this->getDatepickerSettings($field));
    }

    /**
     * Generate Monthnames from locallang
     *
     * @return string
     */
    protected function getDayNames()
    {
        $days = [
            'so',
            'mo',
            'tu',
            'we',
            'th',
            'fr',
            'sa',
        ];
        $dayArray = [];
        foreach ($days as $day) {
            $dayArray[] = LocalizationUtility::translate('datepicker_day_' . $day);
        }
        return implode(',', $dayArray);
    }

    /**
     * Generate Monthnames from locallang
     *
     * @return string
     */
    protected function getMonthNames()
    {
        $months = [
            'jan',
            'feb',
            'mar',
            'apr',
            'may',
            'jun',
            'jul',
            'aug',
            'sep',
            'oct',
            'nov',
            'dec',
        ];
        $monthArray = [];
        foreach ($months as $month) {
            $monthArray[] = LocalizationUtility::translate('datepicker_month_' . $month);
        }
        return implode(',', $monthArray);
    }
}
