<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Extbase\Utility\ArrayUtility as ArrayUtilityExtbase;

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
 * @package In2code\In2publish\Utility
 */
class ArrayUtility extends ArrayUtilityExtbase {

	/**
	 * Returns array with alphabetical letters
	 *
	 * @return array
	 */
	public static function getAbcArray() {
		$arr = array();
		for ($a = A; $a != AA; $a++) {
			$arr[] = $a;
		}
		return $arr;
	}

	/**
	 * Check if String is JSON Array
	 *
	 * @param string $string
	 * @return bool
	 */
	public static function isJsonArray($string) {
		return is_array(json_decode($string, TRUE));
	}
}
