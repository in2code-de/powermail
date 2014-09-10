<?php
namespace In2code\Powermail\Utility\Tca;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Adds a new eval possibility to TCA of TYPO3
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class EvaluateEmail {

	/**
	 * Adds new JavaScript function for evaluation of the TCA fields in backend
	 *
	 * @return 	string		JavaScript
	 */
	public function returnFieldJS() {
		$content = '
			if (validEmail(value)) {
				return value;
			} else {
				return "errorinemail@tryagain.com"
			}

			/**
			 * Check if given String is a valid Email address
			 *
			 * @param	string		An email address
			 * @return	boolean
			 */
			function validEmail(s) {
				var a = false;
				var res = false;
				if(typeof(RegExp) == "function") {
					var b = new RegExp("abc");
					if(b.test("abc") == true){a = true;}
				}
					if (a == true) {
					reg = new RegExp("^([a-zA-Z0-9\\-\\.\\_]+)" + "(\\@)([a-zA-Z0-9\\-\\.]+)" + "(\\.)([a-zA-Z]{2,4})$");
					res = (reg.test(s));
				} else {
					res = (s.search("@") >= 1 && s.lastIndexOf(".") > s.search("@") && s.lastIndexOf(".") >= s.length-5);
				}
				return(res);
			}
		';

		return $content;
	}

	/**
	 * Server valuation
	 *
	 * @param \string $value The field value to be evaluated.
	 * @param \string $isIn The "isIn" value of the field configuration from TCA
	 * @param \bool $set defining if the value is written to the database or not.
	 * @return \string
	 */
	public function evaluateFieldValue($value, $isIn, &$set) {
		if (GeneralUtility::validEmail($value)) {
			$set = 1;
		} else {
			$set = 0;
			$value = 'errorinemail@tryagain.com';
		}
		return $value;
	}

}