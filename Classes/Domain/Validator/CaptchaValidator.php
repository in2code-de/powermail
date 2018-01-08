<?php
declare(strict_types=1);
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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
     */
    public function isValid($mail)
    {
        if ($this->formHasCaptcha($mail->getForm())) {
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if ($answer->getField()->getType() === 'captcha') {
                    $this->setCaptchaArgument(true);
                    if (!$this->validCodePreflight($answer->getValue(), $answer->getField())) {
                        $this->setErrorAndMessage($answer->getField(), 'captcha');
                    }
                }
            }

            // if no captcha arguments given (maybe deleted from DOM)
            if (!$this->hasCaptchaArgument()) {
                $this->addError('captcha', 0);
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
     */
    protected function validCodePreflight($value, $field)
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
     */
    protected function validatePowermailCaptcha($value, Field $field)
    {
        $captchaService = ObjectUtility::getObjectManager()->get(CalculatingCaptchaService::class);
        return $captchaService->validCode($value, $field, $this->isClearSession());
    }

    /**
     * @param string $value
     * @param Field $field
     * @return bool
     */
    protected function validateCaptcha($value, Field $field)
    {
        $captchaVersion = ExtensionManagementUtility::getExtensionVersion('captcha');
        if (VersionNumberUtility::convertVersionNumberToInteger($captchaVersion) >= 2000000) {
            return Utility::checkCaptcha($value, $field->getUid());
        } else {
            return $this->validateCaptchaOld($value);
        }
    }

    /**
     * @param string $value
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function validateCaptchaOld($value)
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
     * @param \In2code\Powermail\Domain\Model\Form $form
     * @return boolean
     */
    protected function formHasCaptcha(Form $form)
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $form = $formRepository->hasCaptcha($form);
        return count($form) ? true : false;
    }

    /**
     * @return boolean
     */
    public function isClearSession()
    {
        return $this->clearSession;
    }

    /**
     * @param boolean $clearSession
     * @return void
     */
    public function setClearSession($clearSession)
    {
        $this->clearSession = $clearSession;
    }

    /**
     * @return boolean
     */
    public function hasCaptchaArgument()
    {
        return $this->captchaArgument;
    }

    /**
     * @param boolean $captchaArgument
     * @return void
     */
    public function setCaptchaArgument($captchaArgument)
    {
        $this->captchaArgument = $captchaArgument;
    }

    /**
     * CaptchaValidator constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        // clear captcha only on create action
        $pluginVariables = GeneralUtility::_GET('tx_powermail_pi1');
        $this->setClearSession(($pluginVariables['action'] === 'create' ? true : false));
    }
}
