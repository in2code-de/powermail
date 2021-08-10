<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class for uploading files and check if they are valid
 */
class UploadValidator extends AbstractValidator
{

    /**
     * Validation of given upload paramaters
     *
     * @param Mail $mail
     * @return bool
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function isValid($mail)
    {
        /** @var UploadService $uploadService */
        $uploadService = ObjectUtility::getObjectManager()->get(UploadService::class);
        foreach ($uploadService->getFiles() as $file) {
            if (!$this->formHasUploadFields() || !$this->basicFileCheck($file)) {
                $file->setValid(false);
                $this->addError('upload_error', 1580681638, ['marker' => $file->getMarker()]);
                $this->setValidState(false);
            }
            if (!$uploadService->isFileExtensionAllowed($file, $this->getAllowedFileExtensions())) {
                $this->setErrorAndMessage($file->getField(), 'upload_extension');
                $file->setValid(false);
            }
            if (!$uploadService->isFileSizeSmallerThenAllowed($file, $this->getMaximumFileSize())) {
                $this->setErrorAndMessage($file->getField(), 'upload_size');
                $file->setValid(false);
            }
        }
        return $this->isValidState();
    }

    /**
     * Check if given form has upload fields
     *
     * @return bool
     * @throws Exception
     */
    protected function formHasUploadFields(): bool
    {
        $arguments = FrontendUtility::getArguments();
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        if (is_string($arguments['mail'])) {
            $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
            $mail = $mailRepository->findByUid((int)$arguments['mail']);
            $form = $formRepository->findByUid((int)$mail->getForm()->getUid());
        } else {
            $form = $formRepository->findByUid((int)$arguments['mail']['form']);
        }
        return $form->hasUploadField();
    }

    /**
     * Basic check if file upload is correct
     *
     * @param File $file
     * @return bool
     */
    protected function basicFileCheck(File $file): bool
    {
        return $file->getField() !== null && $file->getSize() > 0;
    }

    /**
     * @return string
     */
    protected function getAllowedFileExtensions(): string
    {
        return $this->settings['misc']['file']['extension'];
    }

    /**
     * @return int
     */
    protected function getMaximumFileSize(): int
    {
        return (int)$this->settings['misc']['file']['size'];
    }
}
