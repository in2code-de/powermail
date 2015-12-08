<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class DatepickerDataAttributeViewHelper extends AbstractValidationViewHelper
{

    /**
     * Returns Data Attribute Array Datepicker settings (FE + BE)
     *
     * @param Field $field
     * @param array $additionalAttributes To add further attributes
     * @param string $value of this field
     * @return array for data attributes
     */
    public function render(Field $field = null, $additionalAttributes = [], $value = '')
    {
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
