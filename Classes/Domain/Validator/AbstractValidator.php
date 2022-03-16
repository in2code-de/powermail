<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator as ExtbaseAbstractValidator;

/**
 * Class AbstractValidator
 */
abstract class AbstractValidator extends ExtbaseAbstractValidator implements ValidatorInterface
{
    /**
     * Return variable
     *
     * @var bool
     */
    protected $validState = true;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $flexForm;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param Field $field
     * @param string $label
     * @return void
     * @throws Exception
     */
    public function setErrorAndMessage(Field $field, string $label): void
    {
        $this->setValidState(false);
        $this->addError($label, 1580681677, ['marker' => $field->getMarker()]);
    }

    /**
     * Check if javascript validation is activated
     *
     * @return bool
     */
    public function isServerValidationEnabled(): bool
    {
        return $this->settings['validation']['server'] === '1';
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        return true;
    }

    /**
     * @param $validState
     * @return void
     */
    public function setValidState(bool $validState): void
    {
        $this->validState = $validState;
    }

    /**
     * @return bool
     */
    public function isValidState(): bool
    {
        return $this->validState;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @return ValidatorInterface
     */
    public function setConfiguration(array $configuration): ValidatorInterface
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Validation should be in mostly workflows only on first action. This is createAction. But if confirmation is
     * turned on, validation should work in most cases on the confirmationAction.
     *
     * @return bool
     */
    public function isFirstActionForValidation(): bool
    {
        $arguments = FrontendUtility::getArguments();
        if ($this->isConfirmationActivated()) {
            return $arguments['action'] === 'confirmation';
        }
        return $arguments['action'] === 'create';
    }

    /**
     * Constructs the validator and sets validation options
     *
     * @param array $options Options for the validator
     * @throws InvalidValidationOptionsException
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);

        $this->settings = $configurationService->getTypoScriptSettings();
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        $this->flexForm = $flexFormService->convertFlexFormContentToArray(
        // @extensionScannerIgnoreLine Seems to be a false positive: getContentObject() is still correct in 9.0
            $configurationManager->getContentObject()->data['pi_flexform']
        );
    }

    /**
     * @return bool
     */
    public function isConfirmationActivated(): bool
    {
        return $this->flexForm['settings']['flexform']['main']['confirmation'] === '1';
    }
}
