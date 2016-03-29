<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\File;
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
        $this->fillFiles();
        $this->makeUniqueFilenames();
    }

    /**
     * Upload all files to upload folder
     *
     * @return void
     */
    public function uploadAllFiles()
    {
        foreach ($this->getFiles() as $file) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($file, 'in2code: ' . __CLASS__ . ':' . __LINE__);die('hard');
        }
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
     * Prepares files from $_FILES array to $this->files
     *
     * @return void
     */
    protected function fillFiles()
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
     * Create new filenames if needed
     *
     * @return void
     */
    protected function makeUniqueFilenames()
    {
        foreach ($this->getFiles() as $file) {
            if ($this->shouldBeRandomizedFilename($file)) {
                $fileName = $this->randomizeFileName($file->getNewName());
                $file->setNewName($fileName);
            } else {
                $fileName = $file->getNewName();
            }
            if ($this->fileExistsInUploadFolder($file)) {
                $fileName = $this->randomizeFileName($file->getNewName());
                $file->setNewName($fileName);
            }
            $this->fileNames[] = $fileName;
        }
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
        return StringUtility::getRandomString(8) . '.' . $fileInfo['extension'];
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
     * If filenames should be randomized generally
     * or if this filename was used before
     * or if there is a file with this name in the upload folder
     *
     * @param File $file
     * @return bool
     */
    protected function shouldBeRandomizedFilename(File $file)
    {
        return $this->isRandomizeFileNameConfigured() || in_array($file->getNewName(), $this->fileNames);
    }
}
