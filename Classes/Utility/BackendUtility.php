<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
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
class BackendUtility extends BackendUtilityCore
{

    /**
     * Check if backend user is admin
     *
     * @return bool
     */
    public static function isBackendAdmin()
    {
        if (isset($GLOBALS['BE_USER']->user)) {
            return $GLOBALS['BE_USER']->user['admin'] === 1;
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
        if (!empty($GLOBALS['BE_USER']->user[$property])) {
            return $GLOBALS['BE_USER']->user[$property];
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
     * @todo remove condition for TYPO3 6.2 in upcoming major version
     */
    public static function createEditUri($tableName, $identifier, $addReturnUrl = true)
    {
        // use new link generation in backend for TYPO3 7.2 or newer
        if (GeneralUtility::compat_version('7.2')) {
            $uriParameters = array(
                'edit' => array(
                    $tableName => array(
                        $identifier => 'edit'
                    )
                )
            );
            if ($addReturnUrl) {
                $uriParameters['returnUrl'] = self::getReturnUrl();
            }
            $editLink = BackendUtilityCore::getModuleUrl('record_edit', $uriParameters);
        } else {
            $editLink = FrontendUtility::getSubFolderOfCurrentUrl();
            $editLink .= 'typo3/alt_doc.php?edit[' . $tableName . '][' . $identifier . ']=edit';
            if ($addReturnUrl) {
                $editLink .= '&returnUrl=' . self::getReturnUrl();
            }
        }
        return $editLink;
    }

    /**
     * Get return URL from current request
     *
     * @return string
     * @todo remove condition for TYPO3 6.2 in upcoming major version
     */
    protected static function getReturnUrl()
    {
        if (GeneralUtility::compat_version('7.2')) {
            $uri = self::getModuleUrl(self::getModuleName(), self::getCurrentParameters());
        } else {
            $uri = rawurlencode(
                FrontendUtility::getSubFolderOfCurrentUrl() . GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT')
            );
        }
        return $uri;
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
     * @return array
     */
    protected static function getCurrentParameters()
    {
        $parameters = array();
        $ignoreKeys = array(
            'M',
            'moduleToken'
        );
        foreach ((array) GeneralUtility::_GET() as $key => $value) {
            if (in_array($key, $ignoreKeys)) {
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * Read pid from current URL
     *        URL example:
     *        http://powermail.localhost.de/typo3/alt_doc.php?&
     *        returnUrl=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D17%23
     *        element-tt_content-14&edit[tt_content][14]=edit
     *
     * @return int
     */
    public static function getPidFromBackendPage()
    {
        $pid = 0;
        $backUrl = str_replace('?', '&', GeneralUtility::_GP('returnUrl'));
        $urlParts = GeneralUtility::trimExplode('&', $backUrl, true);
        foreach ($urlParts as $part) {
            if (stristr($part, 'id=')) {
                $pid = str_replace('id=', '', $part);
            }
        }
        return (int) $pid;
    }

    /**
     * Returns the URL to a given module
     *      mainly used for visibility settings or deleting
     *      a record via AJAX
     *
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     * @todo remove condition for TYPO3 6.2 in upcoming major version
     */
    public static function getModuleUrl($moduleName, $urlParameters = array())
    {
        if (GeneralUtility::compat_version('7.2')) {
            $uri = parent::getModuleUrl($moduleName, $urlParameters);
        } else {
            $uri = 'tce_db.php?' . parent::getUrlToken('tceAction');
        }
        return $uri;
    }
}
