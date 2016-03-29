<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Basic File Functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class BasicFileUtility extends AbstractUtility
{

    /**
     * Get all Files from a folder
     *
     * @param string $path Relative Path
     * @return array
     */
    public static function getFilesFromRelativePath($path)
    {
        $array = [];
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($path));
        foreach ($files as $file) {
            $array[] = $file;
        }
        return $array;
    }

    /**
     * Add a trailing slash to a string (e.g. path)
     *        folder1/folder2 => folder1/folder2/
     *        folder1/folder2/ => folder1/folder2/
     *
     * @param string $string
     * @return string
     */
    public static function addTrailingSlash($string)
    {
        return rtrim($string, '/') . '/';
    }

    /**
     * Get path from path and filename
     *
     * @param string $pathAndFilename
     * @return string
     */
    public static function getPathFromPathAndFilename($pathAndFilename)
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['dirname'];
    }

    /**
     * Create folder
     *
     * @param $path
     * @return void
     * @throws \Exception
     */
    public static function createFolderIfNotExists($path)
    {
        if (!is_dir($path) && !GeneralUtility::mkdir($path)) {
            throw new \Exception('Folder ' . self::getRelativeFolder($path) . ' does not exists');
        }
    }

    /**
     * Prepend content to the beginning of a file
     *
     * @param string $pathAndFile
     * @param string $content
     * @return void
     */
    public static function prependContentToFile($pathAndFile, $content)
    {
        $absolutePathAndFile = GeneralUtility::getFileAbsFileName($pathAndFile);
        $lines = [];
        if (is_file($absolutePathAndFile)) {
            $lines = file($absolutePathAndFile);
        }
        array_unshift($lines, $content);
        GeneralUtility::writeFile($absolutePathAndFile, implode('', $lines));
    }

    /**
     * Get relative path from absolute path, but don't touch if it's already a relative path
     *
     * @param string $path
     * @return string
     */
    public static function getRelativeFolder($path)
    {
        if (PathUtility::isAbsolutePath($path)) {
            $path = PathUtility::getRelativePathTo($path);
        }
        return $path;
    }
}
