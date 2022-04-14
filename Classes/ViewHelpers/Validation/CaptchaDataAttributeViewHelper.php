<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class CaptchaDataAttributeViewHelper
 */
class CaptchaDataAttributeViewHelper extends ValidationDataAttributeViewHelper
{
    /**
     * Returns Data Attribute Array for JS validation
     *
     * @return array for data attributes
     * @throws Exception
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
            $dataArray['data-powermail-required'] = 'true';
        }
        if ($this->isClientValidationEnabled()) {
            $dataArray['data-powermail-errors-container'] = '.powermail_field_error_container_' . $field->getMarker();
            $dataArray['data-powermail-class-handler'] = '#powermail_field_' . $field->getMarker();
            $dataArray['data-powermail-required-message'] = LocalizationUtility::translate('validationerror_mandatory');
        }

        return $dataArray;
    }
}
