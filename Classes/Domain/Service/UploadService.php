<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\SingletonInterface;
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
 * Class UploadService
 * @package In2code\Powermail\Domain\Service
 */
class UploadService implements SingletonInterface
{

    /**
     * Contains all fileuploads
     *
     * @var File[]
     */
    protected $files = [];

    /**
     * Temporary filenames array to find duplicates
     *
     * @var array
     */
    protected $fileNames = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param array $settings
     * @return void
     */
    public function preflight(array $settings)
    {
        $this->settings = $settings;
        $this->fillFilesFromFilesArray();
        $this->fillFilesFromHiddenFields();
        $this->makeUniqueFilenames();
    }

    /**
     * Upload all files to upload folder
     *
     * @return bool true if files where uploaded correctly
     */
    public function uploadAllFiles()
    {
        $result = false;
        foreach ($this->getFiles() as $file) {
            if (!$file->isUploaded() && $file->isValid()) {
                if ($this->checkExtension($file, $this->getAllowedExtensions())) {
                    if (GeneralUtility::upload_copy_move($file->getTemporaryName(), $file->getNewPathAndFilename())) {
                        $file->setUploaded(true);
                        $result = true;
                    } else {
                        return false;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Is file-extension allowed for uploading?
     *
     * @param File $file
     * @param string $fileExtensions allowed file extensions as commaseparated list
     * @return bool
     */
    public function checkExtension(File $file, $fileExtensions = '')
    {
        $filename = $file->getOriginalName();
        $fileInfo = pathinfo($filename);
        if (
            !empty($fileInfo['extension']) &&
            !empty($fileExtensions) &&
            GeneralUtility::inList($fileExtensions, $fileInfo['extension']) &&
            GeneralUtility::verifyFilenameAgainstDenyPattern($filename) &&
            GeneralUtility::validPathStr($filename)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Is file size smaller than allowed
     *
     * @param File $file
     * @param int $maximumSize
     * @return bool
     */
    public function checkFilesize(File $file, $maximumSize)
    {
        return $file->getSize() <= $maximumSize;
    }

    /**
     * Get all new filenames by given marker (to show filenames on confirmation page again, etc...)
     * If empty, use values from arguments
     *
     * @param string $marker
     * @return array
     */
    public function getNewFileNamesByMarker($marker)
    {
        $newFileNames = [];
        foreach ($this->getFiles() as $file) {
            if ($file->getMarker() === $marker) {
                $newFileNames[] = $file->getNewName();
            }
        }
        return $newFileNames;
    }

    /**
     * Prepares files from $_FILES array to $this->files
     * This will be used by the first submit (before confirmation page will be submitted)
     *
     * @return void
     */
    protected function fillFilesFromFilesArray()
    {
        $filesArray = ObjectUtility::getFilesArray();
        foreach ((array)$filesArray['tx_powermail_pi1']['name']['field'] as $marker => $files) {
            foreach ((array)$files as $key => $originalName) {
                $size = $filesArray['tx_powermail_pi1']['size']['field'][$marker][$key];
                $type = $filesArray['tx_powermail_pi1']['type']['field'][$marker][$key];
                $temporaryName = $filesArray['tx_powermail_pi1']['tmp_name']['field'][$marker][$key];

                /** @var File $file */
                $file = ObjectUtility::getObjectManager()->get(
                    File::class,
                    $marker,
                    $originalName,
                    $size,
                    $type,
                    $temporaryName,
                    $this->getUploadFolder()
                );
                if ($file->validFile()) {
                    $this->addFile($file);
                }
            }
        }
    }

    /**
     * Fill files from hidden field values to $this->files
     * This will happen, if a confirmation page is in use and file values are no more stored in $_FILES
     *
     * @return void
     */
    protected function fillFilesFromHiddenFields()
    {
        if ($this->getFiles() === []) {
            $arguments = $this->getArguments();
            /** @var FieldRepository $fieldRepository */
            $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
            foreach ((array)$arguments['field'] as $marker => $values) {
                $field = $fieldRepository->findByMarkerAndForm($marker, (int)$arguments['mail']['form']);
                if ($field !== null && $field->getType() === 'file' && !empty($values)) {
                    foreach ((array)$values as $value) {
                        /** @var File $file */
                        $file = ObjectUtility::getObjectManager()->get(
                            File::class,
                            $marker,
                            $value,
                            null,
                            null,
                            null,
                            $this->getUploadFolder(),
                            true
                        );
                        if ($file->validFile()) {
                            $this->addFile($file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if given filenames are unique and does not exist in target folder
     * Rename filenames if needed
     *
     * @return void
     */
    protected function makeUniqueFilenames()
    {
        foreach ($this->getFiles() as $file) {
            if (!$file->isUploaded()) {
                $fileName = $file->getNewName();
                if ($this->isRandomizeFileNameConfigured()) {
                    $fileName = $this->randomizeFileName($file->getNewName());
                    $file->setNewName($fileName);
                }
                for ($i = 1; $this->isNotUniqueFilename($file); $i++) {
                    $fileName = $this->makeNewFilenameWithAppendix($file->getNewName(), $i);
                    $file->setNewName($fileName);
                }
                $this->fileNames[] = $fileName;
            }
        }
    }

    /**
     * Create a new filename with an appendix up to _99 than randomize
     *
     *  image.png => image_01.png
     *  image_01.png => image_02.png
     *
     * @param string $filename
     * @param int $iteration
     * @return string
     */
    protected function makeNewFilenameWithAppendix($filename, $iteration)
    {
        if ($iteration >= 100) {
            return $this->randomizeFileName($filename);
        }
        $fileInfo = GeneralUtility::split_fileref($filename);
        $filebody = $this->removeAppendingNumbersInString($fileInfo['filebody']);
        $appendix = '_' . sprintf('%02d', $iteration);
        $filename = $filebody . $appendix . '.' . $fileInfo['fileext'];
        return $filename;
    }

    /**
     * @param File $file
     * @return bool
     */
    protected function fileExistsInUploadFolder(File $file)
    {
        return file_exists($file->getNewPathAndFilename(true));
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function randomizeFileName($filename)
    {
        $fileInfo = pathinfo($filename);
        return StringUtility::getRandomString(32, false) . '.' . $fileInfo['extension'];
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     * @return UploadService
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @param File $file
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;
    }

    /**
     * @return bool
     */
    protected function isRandomizeFileNameConfigured()
    {
        return $this->settings['misc']['file']['randomizeFileName'] === '1';
    }

    /**
     * @return string
     */
    protected function getUploadFolder()
    {
        return $this->settings['misc']['file']['folder'];
    }

    /**
     * @return string
     */
    protected function getAllowedExtensions()
    {
        return $this->settings['misc']['file']['extension'];
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return (array)GeneralUtility::_GP('tx_powermail_pi1');
    }

    /**
     * Remove appending numbers in filename strings
     *        image_01 => image
     *        image_01_02 => image_01
     *
     * @param $string
     * @return mixed
     */
    protected function removeAppendingNumbersInString($string)
    {
        return preg_replace('~_\d+$~', '', $string);
    }

    /**
     * Check if this filename is not unique - could happen if
     * - This filename was used before
     * - Or if there is a file with this name in the target upload folder
     *
     * @param File $file
     * @return bool
     */
    protected function isNotUniqueFilename(File $file)
    {
        return in_array($file->getNewName(), $this->fileNames) || $this->fileExistsInUploadFolder($file);
    }
}
