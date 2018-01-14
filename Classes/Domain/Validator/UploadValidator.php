<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for uploading files and check if they are valid
 */
class UploadValidator extends AbstractValidator
{

    /**
     * Validation of given upload paramaters
     *
     * @param \In2code\Powermail\Domain\Model\Mail $mail
     * @return bool
     */
    public function isValid($mail)
    {
        /** @var UploadService $uploadService */
        $uploadService = ObjectUtility::getObjectManager()->get(UploadService::class);
        foreach ($uploadService->getFiles() as $file) {
            if (!$this->formHasUploadFields() || !$this->basicFileCheck($file)) {
                $file->setValid(false);
                $this->addError('upload_error', $file->getMarker());
                $this->setValidState(false);
            }
            if (!$uploadService->checkExtension($file, $this->getAllowedFileExtensions())) {
                $this->setErrorAndMessage($file->getField(), 'upload_extension');
                $file->setValid(false);
            }
            if (!$uploadService->checkFilesize($file, $this->getMaximumFileSize())) {
                $this->setErrorAndMessage($file->getField(), 'upload_size');
                $file->setValid(false);
            }
        };
        return $this->isValidState();
    }

    /**
     * Check if given form has upload fields
     *
     * @return bool
     */
    protected function formHasUploadFields()
    {
        $arguments = GeneralUtility::_GP('tx_powermail_pi1');
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        /** @var Form $form */
        $form = $formRepository->findByUid((int)$arguments['mail']['form']);
        return $form->hasUploadField();
    }

    /**
     * Basic check if file upload is correct
     *
     * @param File $file
     * @return bool
     */
    protected function basicFileCheck(File $file)
    {
        return $file->getField() !== null && $file->getSize() > 0;
    }

    /**
     * @return string
     */
    protected function getAllowedFileExtensions()
    {
        return $this->settings['misc']['file']['extension'];
    }

    /**
     * @return int
     */
    protected function getMaximumFileSize()
    {
        return (int)$this->settings['misc']['file']['size'];
    }
}
