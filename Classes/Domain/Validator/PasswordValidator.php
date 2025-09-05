<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * Class PasswordValidator
 */
class PasswordValidator extends AbstractValidator
{
    /**
     * Validation of given Params
     *
     * @param Mail $mail
     */
    public function validate($mail): Result
    {
        $this->result = new Result();
        if ($this->formHasPassword($mail->getForm()) && !$this->ignoreValidationIfConfirmation()) {
            $this->isValid($mail);
        }

        return $this->result;
    }

    protected function isValid($mail): void
    {
        foreach ($mail->getAnswers() as $answer) {
            if ($answer->getField()->getType() !== 'password') {
                continue;
            }

            if ($answer->getValue() !== $this->getMirroredValueOfPasswordField($answer->getField())) {
                $this->setErrorAndMessage($answer->getField(), 'password');
            }
        }
    }

    /**
     * Get mirror value from POST params
     */
    protected function getMirroredValueOfPasswordField(Field $field): string
    {
        return FrontendUtility::getArguments()['field'][$field->getMarker() . '_mirror'] ?? '';
    }

    /**
     * Checks if given form has a password field
     */
    protected function formHasPassword(Form $form): bool
    {
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        $form = $formRepository->hasPassword($form);
        return (bool)count($form);
    }

    /**
     * Stop validation if confirmation step is active on create
     */
    protected function ignoreValidationIfConfirmation(): bool
    {
        return (
            !empty(FrontendUtility::getArguments()['__referrer'])
            && !empty(FrontendUtility::getArguments()['action'])
            && FrontendUtility::getArguments()['__referrer']['@action'] === 'confirmation'
            && FrontendUtility::getArguments()['action'] === 'create'
        )
            || (
                !empty(FrontendUtility::getArguments()['controller'])
                && !empty(FrontendUtility::getArguments()['action'])
                && FrontendUtility::getArguments()['controller'] === 'Form'
                && FrontendUtility::getArguments()['action'] === 'optinConfirm'
            );
    }
}
