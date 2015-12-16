<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\CalculatingCaptchaService;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * CaptchaValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
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
                session_start();
                $generatedCaptchaString = $_SESSION['tx_captcha_string'];
                if ($this->isClearSession()) {
                    $_SESSION['tx_captcha_string'] = '';
                }
                if (!empty($value) && $generatedCaptchaString === $value) {
                    return true;
                }
                break;

            default:
                /** @var CalculatingCaptchaService $captchaService */
                $captchaService = $this->objectManager->get(
                    'In2code\\Powermail\\Domain\\Service\\CalculatingCaptchaService'
                );
                if ($captchaService->validCode($value, $field, $this->isClearSession())) {
                    return true;
                }
        }

        return false;
    }

    /**
     * Checks if given form has a captcha
     *
     * @param \In2code\Powermail\Domain\Model\Form $form
     * @return boolean
     */
    protected function formHasCaptcha(Form $form)
    {
        $form = $this->formRepository->hasCaptcha($form);
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
     * Constructor
     */
    public function __construct()
    {
        $pluginVariables = GeneralUtility::_GET('tx_powermail_pi1');
        // clear captcha only on create action
        $this->setClearSession(($pluginVariables['action'] === 'create' ? true : false));
    }
}
