<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Abstract Validation ViewHelper
 */
abstract class AbstractValidationViewHelper extends AbstractViewHelper
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;

    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $contentObject;

    /**
     * Configuration
     */
    protected array $settings = [];

    /**
     * @var string
     */
    protected string $extensionName = '';

    /**
     * Check if native validation is activated
     *
     * @return bool
     */
    protected function isNativeValidationEnabled(): bool
    {
        return !empty($this->settings['validation']['native']) && $this->settings['validation']['native'] === '1';
    }

    /**
     * Check if javascript validation is activated
     *
     * @return bool
     */
    protected function isClientValidationEnabled(): bool
    {
        return !empty($this->settings['validation']['client']) && $this->settings['validation']['client'] === '1';
    }

    /**
     * Set mandatory attributes
     *
     * @param array $additionalAttributes
     * @param Field|null $field
     * @return array
     * @throws Exception
     */
    protected function addMandatoryAttributes(array $additionalAttributes, ?Field $field): array
    {
        if ($field !== null && $field->isMandatory()) {
            if ($this->isNativeValidationEnabled()) {
                $additionalAttributes['required'] = 'required';
            } else {
                if ($this->isClientValidationEnabled()) {
                    $additionalAttributes['data-powermail-required'] = 'true';
                }
            }
            $additionalAttributes['aria-required'] = 'true';

            if ($this->isClientValidationEnabled()) {
                $additionalAttributes['data-powermail-required-message'] =
                    LocalizationUtility::translate('validationerror_mandatory');

                /**
                 * Special case multiselect:
                 * JS sets the error messages after the wrapping div (but only for multiselect)
                 * So we define for this case where the errors should be included
                 */
                if ($field->getType() === 'select' && $field->isMultiselect()) {
                    $additionalAttributes = $this->addErrorContainer($additionalAttributes, $field);
                }
            }
        }
        return $additionalAttributes;
    }

    /**
     * Define where to show errors in markup
     *
     * @param array $additionalAttributes
     * @param Field $field
     * @return array
     * @throws Exception
     */
    protected function addErrorContainer(array $additionalAttributes, Field $field): array
    {
        $additionalAttributes['data-powermail-errors-container'] =
            '.powermail_field_error_container_' . $field->getMarker();
        return $additionalAttributes;
    }

    /**
     * Define where to set the error class in markup
     *
     * @param array $additionalAttributes
     * @param Field $field
     * @return array
     * @throws Exception
     */
    protected function addClassHandler(array $additionalAttributes, Field $field): array
    {
        $additionalAttributes['data-powermail-class-handler'] =
            '.powermail_fieldwrap_' . $field->getMarker() . ' > div > div';
        return $additionalAttributes;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function initialize()
    {
        $this->configurationManager = ObjectUtility::getObjectManager()->get(ConfigurationManagerInterface::class);
        $this->extensionName = 'Powermail';
        // @extensionScannerIgnoreLine Seems to be a false positive: getContentObject() is still correct in 9.0
        $this->contentObject = $this->configurationManager->getContentObject();
        if (isset($this->arguments['extensionName']) && $this->arguments['extensionName'] !== '') {
            $this->extensionName = $this->arguments['extensionName'];
        }
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $this->settings = $configurationService->getTypoScriptSettings();
    }
}
