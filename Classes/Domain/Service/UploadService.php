<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Factory\FileFactory;
use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\FrontendUtility;
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
 */
class UploadService implements SingletonInterface
{
    use SignalTrait;

    /**
     * Contains all files from upload
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
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$this]);
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
                    BasicFileUtility::createFolderIfNotExists($file->getUploadFolder());
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
        $extension = strtolower($fileInfo['extension']);
        if (
            !empty($extension) &&
            !empty($fileExtensions) &&
            GeneralUtility::inList($fileExtensions, $extension) &&
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
     * Prepares files from $_FILES array to $this->files
     * This will be used by the first submit (before confirmation page will be submitted)
     *
     * @return void
     */
    protected function fillFilesFromFilesArray()
    {
        $filesArrayPowermail = ObjectUtility::getFilesArray();
        if (!empty($filesArrayPowermail)) {
            $filesArray = (array)$filesArrayPowermail[FrontendUtility::getPluginName()];
            foreach ((array)$filesArray['name']['field'] as $marker => $files) {
                foreach ((array)array_keys($files) as $key) {
                    /** @var FileFactory $fileFactory */
                    $fileFactory = ObjectUtility::getObjectManager()->get(FileFactory::class, $this->settings);
                    $file = $fileFactory->getInstanceFromFilesArray($filesArray, $marker, $key);
                    if ($file !== null) {
                        $this->addFile($file);
                    }
                }
            }
        }
    }

    /**
     * Fill files from hidden field values to $this->files only if same marker from $_FILES is empty
     * This will happen, if a confirmation page is in use and file values are no more stored in $_FILES per default
     *
     * @return void
     */
    protected function fillFilesFromHiddenFields()
    {
        $arguments = $this->getArguments();
        foreach ((array)$arguments['field'] as $marker => $values) {
            $fileNames = $this->getNewFileNamesByMarker($marker);
            if (empty($fileNames)) {
                foreach ((array)$values as $value) {
                    /** @var FileFactory $fileFactory */
                    $fileFactory = ObjectUtility::getObjectManager()->get(FileFactory::class, $this->settings);
                    $file = $fileFactory->getInstanceFromUploadArguments($marker, $value, $arguments);
                    if ($file !== null) {
                        $this->addFile($file);
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
                    $file->renameName($fileName);
                }
                for ($i = 1; $this->isNotUniqueFilename($file); $i++) {
                    $fileName = $this->makeNewFilenameWithAppendix($file->getNewName(), $i);
                    $file->renameName($fileName);
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
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$this]);
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
    protected function getAllowedExtensions()
    {
        return $this->settings['misc']['file']['extension'];
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return (array)GeneralUtility::_GP(FrontendUtility::getPluginName());
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
