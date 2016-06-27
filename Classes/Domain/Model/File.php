<?php
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\BasicFileUtility;
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
 * File Model for single uploaded files
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class File
{

    /**
     * Field marker name
     *
     * @var string
     */
    protected $marker = '';

    /**
     * Original name
     *
     * @var string
     */
    protected $originalName = '';

    /**
     * Temporary uploaded name
     *
     * @var string|null
     */
    protected $temporaryName = null;

    /**
     * New, cleaned and unique filename
     *
     * @var string
     */
    protected $newName = '';

    /**
     * Is there a problem with this file?
     *
     * @var bool
     */
    protected $valid = true;

    /**
     * Like "image/png"
     *
     * @var string
     */
    protected $type = '';

    /**
     * Filesize
     *
     * @var int
     */
    protected $size = 0;

    /**
     * Uploadfolder for this file
     *
     * @var string
     */
    protected $uploadFolder = 'uploads/tx_powermail/';

    /**
     * Already uploaded to uploadfolder?
     *
     * @var bool
     */
    protected $uploaded = false;

    /**
     * File must be renamed?
     *
     * @var bool
     */
    protected $renamed = false;

    /**
     * Related field
     *
     * @var Field|null
     */
    protected $field = null;

    /**
     * @param string $marker
     * @param string $originalName
     * @param string $temporaryName
     */
    public function __construct($marker, $originalName, $temporaryName)
    {
        $this->marker = $marker;
        $this->originalName = $originalName;
        $this->temporaryName = $temporaryName;
    }

    /**
     * @return string
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @param string $marker
     * @return File
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     * @return File
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryName()
    {
        return $this->temporaryName;
    }

    /**
     * @param string $temporaryName
     * @return File
     */
    public function setTemporaryName($temporaryName)
    {
        $this->temporaryName = $temporaryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewName()
    {
        return $this->newName;
    }

    /**
     * @param string $newName
     * @return File
     */
    public function setNewName($newName)
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Set a new name and set renamed to true
     *
     * @param string $newName
     * @return File
     */
    public function renameName($newName)
    {
        $this->newName = $newName;
        $this->setRenamed(true);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     * @return File
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadFolder()
    {
        return $this->uploadFolder;
    }

    /**
     * @param string $uploadFolder
     * @return File
     */
    public function setUploadFolder($uploadFolder)
    {
        $this->uploadFolder = BasicFileUtility::addTrailingSlash($uploadFolder);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUploaded()
    {
        return $this->uploaded;
    }

    /**
     * @param boolean $uploaded
     * @return File
     */
    public function setUploaded($uploaded)
    {
        $this->uploaded = $uploaded;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRenamed()
    {
        return $this->renamed;
    }

    /**
     * @param boolean $renamed
     * @return File
     */
    public function setRenamed($renamed)
    {
        $this->renamed = $renamed;
        return $this;
    }

    /**
     * @return Field|null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field|null $field
     * @return File
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return bool
     */
    public function validFile()
    {
        return $this->getSize() > 0 && $this->getOriginalName();
    }

    /**
     * Check if file is existing on the server
     *
     * @return bool
     */
    public function isFileExisting()
    {
        return $this->isUploaded() && file_exists($this->getNewPathAndFilename(true));
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getNewPathAndFilename($absolute = false)
    {
        $pathAndFilename = $this->getUploadFolder() . $this->getNewName();
        if ($absolute) {
            $pathAndFilename = GeneralUtility::getFileAbsFileName($pathAndFilename);
        }
        return $pathAndFilename;
    }
}
