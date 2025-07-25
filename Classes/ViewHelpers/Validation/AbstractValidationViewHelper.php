<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Validation;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\LocalizationUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Abstract Validation ViewHelper
 */
abstract class AbstractValidationViewHelper extends AbstractViewHelper
{
    protected ConfigurationManagerInterface $configurationManager;

    protected ?ContentObjectRenderer $contentObject = null;

    /**
     * Configuration
     */
    protected array $settings = [];

    protected string $extensionName = '';

    /**
     * Check if native validation is activated
     */
    protected function isNativeValidationEnabled(): bool
    {
        return !empty($this->settings['validation']['native']) && $this->settings['validation']['native'] === '1';
    }

    /**
     * Check if javascript validation is activated
     */
    protected function isClientValidationEnabled(): bool
    {
        return !empty($this->settings['validation']['client']) && $this->settings['validation']['client'] === '1';
    }

    protected function addMandatoryAttributes(array $additionalAttributes, ?Field $field): array
    {
        if ($field instanceof \In2code\Powermail\Domain\Model\Field && $field->isMandatory()) {
            if ($this->isNativeValidationEnabled()) {
                $additionalAttributes['required'] = 'required';
            } elseif ($this->isClientValidationEnabled()) {
                $additionalAttributes['data-powermail-required'] = 'true';
            }

            $additionalAttributes['aria-required'] = 'true';

            if ($this->isClientValidationEnabled()) {
                $mandatoryText = $field->getMandatoryText() !== ''
                    ? $field->getMandatoryText()
                    : LocalizationUtility::translate('validationerror_mandatory');

                $additionalAttributes['data-powermail-required-message'] =
                    $mandatoryText;

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

    protected function addErrorContainer(array $additionalAttributes, Field $field): array
    {
        $additionalAttributes['data-powermail-errors-container'] =
            '.powermail_field_error_container_' . $field->getMarker();
        return $additionalAttributes;
    }

    protected function addClassHandler(array $additionalAttributes, Field $field): array
    {
        $additionalAttributes['data-powermail-class-handler'] =
            '.powermail_fieldwrap_' . $field->getMarker() . ' > div > div';
        return $additionalAttributes;
    }

    public function initialize(): void
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->extensionName = 'Powermail';

        $request = $this->getRequest();
        if ($request instanceof ServerRequestInterface) {
            $this->contentObject = $request->getAttribute('currentContentObject');
        }

        if (isset($this->arguments['extensionName']) && $this->arguments['extensionName'] !== '') {
            $this->extensionName = $this->arguments['extensionName'];
        }

        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->settings = $configurationService->getTypoScriptSettings();
    }

    protected function getRequest(): ?ServerRequestInterface
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }
}
