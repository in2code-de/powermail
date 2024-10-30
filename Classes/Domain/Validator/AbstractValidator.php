<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator as ExtbaseAbstractValidator;

/**
 * Class AbstractValidator
 */
abstract class AbstractValidator extends ExtbaseAbstractValidator implements ValidatorInterface
{
    /**
     * Return variable
     */
    protected bool $validState = true;

    protected array $settings;

    protected array $flexForm;

    protected array $configuration = [];

    public function setErrorAndMessage(Field $field, string $label): void
    {
        $this->setValidState(false);
        $this->result->addError(new Error($label, 1580681677, ['marker' => $field->getMarker()]));
    }

    /**
     * Check if javascript validation is activated
     */
    public function isServerValidationEnabled(): bool
    {
        return $this->settings['validation']['server'] === '1';
    }

    public function initialize(): void
    {
    }

    public function setValidState(bool $validState): void
    {
        $this->validState = $validState;
    }

    public function isValidState(): bool
    {
        return $this->validState;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): ValidatorInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Validation should be in mostly workflows only on first action. This is createAction. But if confirmation is
     * turned on, validation should work in most cases on the confirmationAction.
     *
     * ToDo: for v13 rename the actions in the condition
     */
    public function isFirstActionForValidation(): bool
    {
        $arguments = FrontendUtility::getArguments();
        if ($this->isConfirmationActivated()) {
            return $arguments['action'] === 'checkConfirmation';
        }

        return $arguments['action'] === 'checkCreate';
    }

    /**
     * Constructs the validator and sets validation options
     *
     * @param array $options Options for the validator
     * @throws InvalidValidationOptionsException
     */
    public function __construct()
    {
        GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->settings = $configurationService->getTypoScriptSettings();
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->flexForm = $flexFormService->convertFlexFormContentToArray(
            // added check for the array key for `pi_flexform` due to https://github.com/in2code-de/powermail/issues/1020
            // please be aware, if you include powermail via TypoScript, you are on your own to set all necessary values
            // @extensionScannerIgnoreLine Seems to be a false positive: getContentObject() is still correct in 9.0
            $this->request->getAttribute('currentContentObject')->data['pi_flexform'] ?? ''
        );
    }

    public function isConfirmationActivated(): bool
    {
        return $this->flexForm['settings']['flexform']['main']['confirmation'] === '1';
    }
}
