<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UploadAttributesViewHelper
 */
class UploadAttributesViewHelper extends AbstractValidationViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('field', Field::class, 'Field', true);
        $this->registerArgument('additionalAttributes', 'array', 'additionalAttributes', false, []);
    }

    /**
     * Array for multiple upload
     *
     * @throws DBALException
     */
    public function render(): array
    {
        /** @var Field $field */
        $field = $this->arguments['field'];
        $additionalAttributes = $this->arguments['additionalAttributes'];

        $additionalAttributes = $this->addMandatoryAttributes($additionalAttributes, $field);
        if ($field->getMultiselectForField()) {
            $additionalAttributes['multiple'] = 'multiple';
        }

        if (!empty($this->settings['misc']['file']['extension'])) {
            $additionalAttributes['accept'] =
                $this->getDottedListOfExtensions($this->settings['misc']['file']['extension']);
        }

        if ($this->isClientValidationEnabled()) {
            if (!empty($this->settings['misc']['file']['size'])) {
                $additionalAttributes['data-powermail-powermailfilesize'] =
                    (int)$this->settings['misc']['file']['size'] . ',' . $field->getMarker();
                $additionalAttributes['data-powermail-powermailfilesize-message'] =
                    LocalizationUtility::translate('validationerror_upload_size');
            }

            if (!empty($this->settings['misc']['file']['extension'])) {
                $additionalAttributes['data-powermail-powermailfileextensions'] = $field->getMarker();
                $additionalAttributes['data-powermail-powermailfileextensions-message'] =
                    LocalizationUtility::translate('validationerror_upload_extension');
            }
        }

        return $additionalAttributes;
    }

    /**
     * Get extensions with dot as prefix
     *      before: jpg,png,gif
     *      after: .jpg,.png,.gif
     */
    protected function getDottedListOfExtensions(string $extensionList): string
    {
        $extensions = GeneralUtility::trimExplode(',', $extensionList, true);
        $dottedList = implode(',.', $extensions);
        if ($dottedList !== '' && $dottedList !== '0') {
            return '.' . $dottedList;
        }

        return $dottedList;
    }
}
