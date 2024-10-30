<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;

/**
 * Class DatepickerDataAttributeViewHelper
 */
class DatepickerDataAttributeViewHelper extends AbstractValidationViewHelper
{
    public function initializeArguments(): void
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
     * @throws DBALException
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

        return $this->addMandatoryAttributes($additionalAttributes, $field);
    }

    /**
     * Get Datepicker Settings
     *
     * @param Field|null $field
     */
    protected function getDatepickerSettings(Field $field = null): string
    {
        if (!$field instanceof \In2code\Powermail\Domain\Model\Field) {
            return 'datetime';
        }

        return $field->getDatepickerSettings();
    }

    /**
     * @param Field|null $field
     */
    protected function getFormat(Field $field = null): string
    {
        $format = LocalizationUtility::translate('datepicker_format_' . $this->getDatepickerSettings($field));
        return $this->convertFormatForMomentJs($format);
    }

    /**
     * Because moment.js needs a different format writings, we need to convert this
     * "Y-m-d H:i" => "YYYY-MM-DD HH:mm"
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
            ],
        ];
        return str_replace($replace[0], $replace[1], $format);
    }
}
