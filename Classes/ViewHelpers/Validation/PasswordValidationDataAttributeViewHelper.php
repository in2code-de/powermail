<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class PasswordValidationDataAttributeViewHelper
 */
class PasswordValidationDataAttributeViewHelper extends ValidationDataAttributeViewHelper
{
    /**
     * Returns Data Attribute Array for JS validation
     *
     * @return array for data attributes
     * @throws Exception
     */
    public function render(): array
    {
        $additionalAttributes = parent::render();

        if ($this->isClientValidationEnabled()) {
            /** @var Field $field */
            $field = $this->arguments['field'];
            $additionalAttributes['data-powermail-equalto'] = '#powermail_field_' . $field->getMarker();
            $additionalAttributes['data-powermail-equalto-message'] =
                LocalizationUtility::translate('validationerror_password');
        }

        return $additionalAttributes;
    }
}
