<?php
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class for uploading files and check if they are valid
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
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
