<?php
namespace In2code\Powermail\Domain\Factory;

use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
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
 * Class FileFactory
 * @package In2code\Powermail\Domain\Service
 */
class FileFactory
{

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get instance of File from Files Array
     *
     * @param array $filesArray normally $_FILES['tx_powermail_pi1']
     * @param string $marker
     * @param string $key
     * @return File|null
     */
    public function getInstanceFromFilesArray(array $filesArray, $marker, $key)
    {
        $originalName = $filesArray['name']['field'][$marker][$key];
        $size = $filesArray['size']['field'][$marker][$key];
        $type = $filesArray['type']['field'][$marker][$key];
        $temporaryName = $filesArray['tmp_name']['field'][$marker][$key];
        if (!empty($originalName) && !empty($temporaryName) && $size > 0) {
            return $this->makeFileInstance($marker, $originalName, $size, $type, $temporaryName);
        }
        return null;
    }

    /**
     * Get instance of File from arguments
     *
     * @param string $marker
     * @param string $value
     * @param array $arguments
     * @return File|null
     */
    public function getInstanceFromUploadArguments($marker, $value, array $arguments)
    {
        /** @var FieldRepository $fieldRepository */
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $field = $fieldRepository->findByMarkerAndForm($marker, (int)$arguments['mail']['form']);
        if ($field !== null && $field->dataTypeFromFieldType($field->getType()) === 3 && !empty($value)) {
            return $this->makeFileInstance($marker, $value, null, null, null, true);
        }
        return null;
    }

    /**
     * Get File instance
     *
     * @param string $marker
     * @param string $originalName
     * @param int $size
     * @param string $type
     * @param string $temporaryName
     * @param bool $uploaded
     * @return File
     */
    protected function makeFileInstance(
        $marker,
        $originalName,
        $size = null,
        $type = null,
        $temporaryName = null,
        $uploaded = false
    ) {
        /** @var File $file */
        $file = ObjectUtility::getObjectManager()->get(File::class, $marker, $originalName, $temporaryName);
        $file->setNewName(StringUtility::cleanString($originalName));
        $file->setUploadFolder($this->getUploadFolder());
        if ($size === null) {
            $size = filesize($file->getNewPathAndFilename(true));
        }
        $file->setSize($size);
        if ($type === null) {
            $type = mime_content_type($file->getNewPathAndFilename(true));
        }
        $file->setType($type);
        $file->setUploaded($uploaded);

        /* @var FieldRepository $fieldRepository */
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $file->setField($fieldRepository->findByMarkerAndForm($marker, $this->getFormUid()));
        return $file;
    }

    /**
     * @return string
     */
    protected function getUploadFolder()
    {
        return $this->settings['misc']['file']['folder'];
    }

    /**
     * @return int
     */
    protected function getFormUid()
    {
        $arguments = $this->getArguments();
        return (int)$arguments['mail']['form'];
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return (array)GeneralUtility::_GP('tx_powermail_pi1');
    }
}
