<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Exception\DeprecatedException;
use TYPO3\CMS\Core\Package\Exception as ExceptionCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;

/**
 * Class CaptchaValidator
 */
class CaptchaValidator extends AbstractValidator
{
    /**
     * Captcha Session clean (only if mail is out)
     */
    protected bool $clearSession = true;

    /**
     * Any Captcha arguments found?
     */
    protected bool $captchaArgument = false;

    /**
     * Validation of given Params
     *
     * @param Mail $mail
     * @throws ExceptionCore
     * @throws DeprecatedException
     */
    protected function isValid($mail): void
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
    }

    /**
     * Check if given string is correct
     *
     * @throws ExceptionCore
     */
    protected function validCodePreflight(string $value, Field $field): bool
    {
        $result = $this->validatePowermailCaptcha($value, $field);

        return $result;
    }

    protected function validatePowermailCaptcha(string $value, Field $field): bool
    {
        $captchaService = GeneralUtility::makeInstance(CalculatingCaptchaService::class);
        return $captchaService->validCode($value, $field, $this->isClearSession());
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function validateCaptchaOld(string $value): bool
    {
        session_start();
        $captchaString = $_SESSION['tx_captcha_string'];
        if ($this->isClearSession()) {
            $_SESSION['tx_captcha_string'] = '';
        }

        return $value !== '' && $value !== '0' && $captchaString === $value;
    }

    /**
     * Checks if given form has a captcha
     */
    protected function formHasCaptcha(Form $form): bool
    {
        /** @var FormRepository $formRepository */
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        $form = $formRepository->hasCaptcha($form);
        return (bool)count($form);
    }

    public function isClearSession(): bool
    {
        return $this->clearSession;
    }

    public function setClearSession(bool $clearSession): void
    {
        $this->clearSession = $clearSession;
    }

    public function hasCaptchaArgument(): bool
    {
        return $this->captchaArgument;
    }

    public function setCaptchaArgument(bool $captchaArgument): void
    {
        $this->captchaArgument = $captchaArgument;
    }

    /**
     * @param array $options
     * @throws InvalidValidationOptionsException
     */
    public function __construct()
    {
        // clear captcha only on create action
        $pluginVariables = $GLOBALS['TYPO3_REQUEST']->getQueryParams()['tx_powermail_pi1'];
        $this->setClearSession($pluginVariables['action'] === 'create');
    }
}
