<?php
declare(strict_types=1);
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
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('ttContentUid', 'int', 'UID of content element', false);
    }

    /**
     * Returns Data Attribute Array for JS validation with parsley.js
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
            $ttContentUid = $this->arguments['ttContentUid'];
            $additionalAttributes['data-parsley-equalto'] = '#powermail_field_' . $field->getMarker();
            if ($ttContentUid) {
                $additionalAttributes['data-parsley-equalto'] = $additionalAttributes['data-parsley-equalto'] . '_' . $ttContentUid;
            }
            $additionalAttributes['data-parsley-equalto-message'] =
                LocalizationUtility::translate('validationerror_password');
        }

        return $additionalAttributes;
    }
}
