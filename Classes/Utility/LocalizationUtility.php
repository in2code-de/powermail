<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as LocalizationUtilityExtbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 in2code.de
 *  Alex Kellner <alexander.kellner@in2code.de>
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
 * Class ArrayUtility
 *
 * @package In2code\Powermail\Utility
 */
class LocalizationUtility extends AbstractUtility
{

    /**
     * Translate function with predefined extensionName
     * Could also be used together with unit tests
     *
     * @param string $key
     * @param string $extensionName
     * @param null $arguments
     * @return string
     */
    public static function translate($key, $extensionName = 'powermail', $arguments = null)
    {
        $databaseConnection = self::getDatabaseConnection();
        if (empty($databaseConnection)) {
            if (stristr($key, 'datepicker_format')) {
                return 'Y-m-d H:i';
            }
            return $key;
        }
        return LocalizationUtilityExtbase::translate($key, $extensionName, $arguments);
    }
}
