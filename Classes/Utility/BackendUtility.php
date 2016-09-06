<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Backend utility functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class BackendUtility extends AbstractUtility
{

    /**
     * Check if backend user is admin
     *
     * @return bool
     */
    public static function isBackendAdmin()
    {
        if (isset(self::getBackendUserAuthentication()->user)) {
            return self::getBackendUserAuthentication()->user['admin'] === 1;
        }
        return false;
    }

    /**
     * Get property from backend user
     *
     * @param string $property
     * @return string
     */
    public static function getPropertyFromBackendUser($property = 'uid')
    {
        if (!empty(self::getBackendUserAuthentication()->user[$property])) {
            return self::getBackendUserAuthentication()->user[$property];
        }
        return '';
    }

    /**
     * Create an URI to edit any record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createEditUri($tableName, $identifier, $addReturnUrl = true)
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $identifier => 'edit'
                ]
            ]
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return BackendUtilityCore::getModuleUrl('record_edit', $uriParameters);
    }

    /**
     * Create an URI to add a new record
     *
     * @param string $tableName
     * @param int $pageIdentifier where to save the new record
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createNewUri($tableName, $pageIdentifier, $addReturnUrl = true)
    {
        $uriParameters = [
            'edit' => [
                $tableName => [
                    $pageIdentifier => 'new'
                ]
            ]
        ];
        if ($addReturnUrl) {
            $uriParameters['returnUrl'] = self::getReturnUrl();
        }
        return BackendUtilityCore::getModuleUrl('record_edit', $uriParameters);
    }

    /**
     * Get return URL from current request
     *
     * @return string
     */
    protected static function getReturnUrl()
    {
        return self::getModuleUrl(self::getModuleName(), self::getCurrentParameters());
    }

    /**
     * Get module name
     *
     * @return string
     */
    protected static function getModuleName()
    {
        return (string) GeneralUtility::_GET('M');
    }

    /**
     * Get all GET/POST params without module name and token
     *
     * @param array $getParameters
     * @return array
     */
    public static function getCurrentParameters($getParameters = [])
    {
        if (empty($getParameters)) {
            $getParameters = GeneralUtility::_GET();
        }
        $parameters = [];
        $ignoreKeys = [
            'M',
            'moduleToken'
        ];
        foreach ($getParameters as $key => $value) {
            if (in_array($key, $ignoreKeys)) {
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * Read pid from returnUrl
     *        URL example:
     *        http://powermail.localhost.de/typo3/alt_doc.php?&
     *        returnUrl=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D17%23
     *        element-tt_content-14&edit[tt_content][14]=edit
     *
     * @param string $returnUrl normally used for testing
     * @return int
     */
    public static function getPidFromBackendPage($returnUrl = '')
    {
        if (empty($returnUrl)) {
            $returnUrl = GeneralUtility::_GP('returnUrl');
        }
        $urlParts = parse_url($returnUrl);
        parse_str($urlParts['query'], $queryParts);
        return (int)$queryParts['id'];
    }

    /**
     * Returns the URL to a given module
     *      mainly used for visibility settings or deleting
     *      a record via AJAX
     *
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     */
    public static function getModuleUrl($moduleName, $urlParameters = [])
    {
        return BackendUtilityCore::getModuleUrl($moduleName, $urlParameters);
    }

    /**
     * Returns the Page TSconfig for page with id, $id
     *
     * @param int $pid
     * @param array $rootLine
     * @param bool $returnPartArray
     * @return array Page TSconfig
     * @see \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser
     */
    public static function getPagesTSconfig($pid, $rootLine = null, $returnPartArray = false)
    {
        return BackendUtilityCore::getPagesTSconfig($pid, $rootLine, $returnPartArray);
    }
}
