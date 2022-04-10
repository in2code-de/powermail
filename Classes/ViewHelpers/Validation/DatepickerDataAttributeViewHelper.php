<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Object\Exception;

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
     * @throws Exception
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $additionalAttributes = $this->arguments['additionalAttributes'];
        $value = $this->arguments['value'];

        $additionalAttributes['data-datepicker-format'] = $this->getFormat($field);
        if ($value) {
            $additionalAttributes['data-date-value'] = $value;
        }

        $additionalAttributes = $this->addMandatoryAttributes($additionalAttributes, $field);

        return $additionalAttributes;
    }

    /**
     * Get Datepicker Settings
     *
     * @param Field|null $field
     * @return string
     */
    protected function getDatepickerSettings(Field $field = null): string
    {
        if ($field === null) {
            return 'datetime';
        }
        return $field->getDatepickerSettings();
    }

    /**
     * @param Field|null $field
     * @return string
     */
    protected function getFormat(Field $field = null): string
    {
        $format = LocalizationUtility::translate('datepicker_format_' . $this->getDatepickerSettings($field));
        return $this->convertFormatForMomentJs($format);
    }

    /**
     * Because moment.js needs a different format writings, we need to convert this
     * "Y-m-d H:i" => "YYYY-MM-DD HH:mm"
     * @param string $format
     * @return string
     */
    protected function convertFormatForMomentJs(string $format): string
    {
        $replace = [
            [
                'Y',
                'm',
                'd',
                'H',
                'i',
            ],
            [
                'YYYY',
                'MM',
                'DD',
                'HH',
                'mm',
            ]
        ];
        return str_replace($replace[0], $replace[1], $format);
    }
}
