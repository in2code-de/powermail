<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use ThinkopenAt\Captcha\Utility;
use TYPO3\CMS\Core\Package\Exception as ExceptionCore;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;

/**
 * Class CaptchaValidator
 */
class CaptchaValidator extends AbstractValidator
{

    /**
     * Captcha Session clean (only if mail is out)
     *
     * @var bool
     */
    protected $clearSession = true;

    /**
     * Any Captcha arguments found?
     *
     * @var bool
     */
    protected $captchaArgument = false;

    /**
     * Validation of given Params
     *
     * @param Mail $mail
     * @return bool
     * @throws Exception
     * @throws ExceptionCore
     */
    public function isValid($mail)
    {
        if ($this->formHasCaptcha($mail->getForm())) {
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if ($answer->getField()->getType() === 'captcha') {
                    $this->setCaptchaArgument(true);
                    /* If the answer has a UID it has already been validated an persisted.
                     * There's no reason to validate it twice. Also, there's no possibility, since the value to check
                     * against got removed from the user's session on the first validation.
                     * Resolves: https://github.com/einpraegsam/powermail/issues/376
                     * Resolves: https://projekte.in2code.de/issues/44174
                     */
                    if ($answer->getUid() === null) {
                        if (!$this->validCodePreflight($answer->getValue(), $answer->getField())) {
                            $this->setErrorAndMessage($answer->getField(), 'captcha');
                        }
                    }
                }
            }

            // if no captcha arguments given (maybe deleted from DOM)
            if (!$this->hasCaptchaArgument()) {
                $this->addError('captcha', 1580681526);
                $this->setValidState(false);
            }
        }

        return $this->isValidState();
    }

    /**
     * Check if given string is correct
     *
     * @param string $value
     * @param Field $field
     * @return bool
     * @throws Exception
     * @throws ExceptionCore
     */
    protected function validCodePreflight(string $value, Field $field): bool
    {
        switch (TypoScriptUtility::getCaptchaExtensionFromSettings($this->settings)) {
            case 'captcha':
                $result = $this->validateCaptcha($value, $field);
                break;

            default:
                $result = $this->validatePowermailCaptcha($value, $field);
        }
        return $result;
    }

    /**
     * @param string $value
     * @param Field $field
     * @return bool
     * @throws Exception
     */
    protected function validatePowermailCaptcha(string $value, Field $field): bool
    {
        $captchaService = ObjectUtility::getObjectManager()->get(CalculatingCaptchaService::class);
        return $captchaService->validCode($value, $field, $this->isClearSession());
    }

    /**
     * @param string $value
     * @param Field $field
     * @return bool
     * @throws ExceptionCore
     */
    protected function validateCaptcha(string $value, Field $field): bool
    {
        $captchaVersion = ExtensionManagementUtility::getExtensionVersion('captcha');
        if (VersionNumberUtility::convertVersionNumberToInteger($captchaVersion) >= 2000000) {
            return Utility::checkCaptcha($value, $field->getUid());
        }
        return $this->validateCaptchaOld($value);
    }

    /**
     * @param string $value
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function validateCaptchaOld(string $value): bool
    {
        session_start();
        $captchaString = $_SESSION['tx_captcha_string'];
        if ($this->isClearSession()) {
            $_SESSION['tx_captcha_string'] = '';
        }
        return !empty($value) && $captchaString === $value;
    }

    /**
     * Checks if given form has a captcha
     *
     * @param Form $form
     * @return bool
     * @throws Exception
     */
    protected function formHasCaptcha(Form $form): bool
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $form = $formRepository->hasCaptcha($form);
        return count($form) ? true : false;
    }

    /**
     * @return bool
     */
    public function isClearSession(): bool
    {
        return $this->clearSession;
    }

    /**
     * @param bool $clearSession
     * @return void
     */
    public function setClearSession(bool $clearSession): void
    {
        $this->clearSession = $clearSession;
    }

    /**
     * @return bool
     */
    public function hasCaptchaArgument(): bool
    {
        return $this->captchaArgument;
    }

    /**
     * @param bool $captchaArgument
     * @return void
     */
    public function setCaptchaArgument($captchaArgument): void
    {
        $this->captchaArgument = $captchaArgument;
    }

    /**
     * @param array $options
     * @throws InvalidValidationOptionsException
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        // clear captcha only on create action
        $pluginVariables = GeneralUtility::_GET('tx_powermail_pi1');
        $this->setClearSession(($pluginVariables['action'] === 'create' ? true : false));
    }
}
