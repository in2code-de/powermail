<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PasswordValidator
 */
class PasswordValidator extends AbstractValidator
{

    /**
     * Validation of given Params
     *
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        if (!$this->formHasPassword($mail->getForm()) || $this->ignoreValidationIfConfirmation()) {
            return true;
        }

        foreach ($mail->getAnswers() as $answer) {
            if ($answer->getField()->getType() !== 'password') {
                continue;
            }
            if ($answer->getValue() !== $this->getMirroredValueOfPasswordField($answer->getField())) {
                $this->setErrorAndMessage($answer->getField(), 'password');
            }

        }

        return $this->isValidState();
    }

    /**
     * Get mirror value from POST params
     *
     * @param Field $field
     * @return string
     */
    protected function getMirroredValueOfPasswordField(Field $field)
    {
        $piVars = GeneralUtility::_GP($this->variablesPrefix);
        $mirroredValue = $piVars['field'][$field->getMarker() . '_mirror'];
        return $mirroredValue;
    }

    /**
     * Checks if given form has a password field
     *
     * @param Form $form
     * @return boolean
     */
    protected function formHasPassword(Form $form)
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $form = $formRepository->hasPassword($form);
        return count($form) ? true : false;
    }

    /**
     * Stop validation if confirmation step is active on create
     *
     * @return bool
     */
    protected function ignoreValidationIfConfirmation()
    {
        $piVars = GeneralUtility::_GP($this->variablesPrefix);
        $piVarsGet = GeneralUtility::_GET($this->variablesPrefix);
        if ($piVars['__referrer']['@action'] === 'confirmation' && $piVarsGet['action'] === 'create') {
            return true;
        }
        return false;
    }
}
