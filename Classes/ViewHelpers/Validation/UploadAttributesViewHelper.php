<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UploadAttributesViewHelper
 */
class UploadAttributesViewHelper extends AbstractValidationViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('additionalAttributes', 'array', 'additionalAttributes', false, []);
    }

    /**
     * Array for multiple upload
     *
     * @return array
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $additionalAttributes = $this->arguments['additionalAttributes'];

        $this->addMandatoryAttributes($additionalAttributes, $field);
        if ($field->getMultiselectForField()) {
            $additionalAttributes['multiple'] = 'multiple';
        }
        if (!empty($this->settings['misc']['file']['extension'])) {
            $additionalAttributes['accept'] =
                $this->getDottedListOfExtensions($this->settings['misc']['file']['extension']);
        }
        if ($this->isClientValidationEnabled()) {
            if (!empty($this->settings['misc']['file']['size'])) {
                $additionalAttributes['data-parsley-powermailfilesize'] =
                    (int)$this->settings['misc']['file']['size'] . ',' . $field->getMarker();
                $additionalAttributes['data-parsley-powermailfilesize-message'] =
                    LocalizationUtility::translate('validationerror_upload_size');
            }
            if (!empty($this->settings['misc']['file']['extension'])) {
                $additionalAttributes['data-parsley-powermailfileextensions'] = $field->getMarker();
                $additionalAttributes['data-parsley-powermailfileextensions-message'] =
                    LocalizationUtility::translate('validationerror_upload_extension');
            }
        }
        return $additionalAttributes;
    }

    /**
     * Get extensions with dot as prefix
     *      before: jpg,png,gif
     *      after: .jpg,.png,.gif
     *
     * @param string $extensionList
     * @return string
     */
    protected function getDottedListOfExtensions(string $extensionList): string
    {
        $extensions = GeneralUtility::trimExplode(',', $extensionList, true);
        $dottedList = implode(',.', $extensions);
        if (!empty($dottedList)) {
            $dottedList = '.' . $dottedList;
        }
        return $dottedList;
    }
}
