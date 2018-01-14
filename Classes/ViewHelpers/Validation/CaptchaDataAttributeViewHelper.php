<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;

/**
 * Class CaptchaDataAttributeViewHelper
 */
class CaptchaDataAttributeViewHelper extends ValidationDataAttributeViewHelper
{

    /**
     * Returns Data Attribute Array for JS validation with parsley.js
     *
     * @return array for data attributes
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        if ($field->getType() !== 'captcha') {
            return $this->arguments['additionalAttributes'];
        }
        $dataArray = parent::render();

        if ($this->isNativeValidationEnabled()) {
            $dataArray['required'] = 'required';
        } elseif ($this->isClientValidationEnabled()) {
            $dataArray['data-parsley-required'] = 'true';
        }
        if ($this->isClientValidationEnabled()) {
            $dataArray['data-parsley-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
            $dataArray['data-parsley-class-handler'] = '#powermail_field_' . $field->getMarker();
            $dataArray['data-parsley-required-message'] = LocalizationUtility::translate('validationerror_mandatory');
        }

        return $dataArray;
    }
}
