<?php

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
 * Div is a class for a collection of misc functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Utility_CalculatingCaptcha {

	/**
	 * @var 	array		TypoScript
	 */
	protected $conf;

	/**
	 * @var 	string		New Image Path
	 */
	public $captchaImage = 'EXT:powermail/Resources/Public/Image/captcha.png'; // Path to captcha image

	/**
	 * Render Link to Captcha Image
	 */
	public function render($conf) {
		$this->conf = $conf;
		$string = $this->getString(); // get random string for captcha
		$content = $this->createImage($string); // create image
		return $content;
	}

	/**
	 * Check if given code is correct
	 *
	 * @param	string		$code String to compare
	 * @param	boolean		$clearSession Flag if session should be cleared or not
	 * @return	boolean
	 */
	public function validCode($code, $clearSession = 1) {
		$valid = 0;
		if (intval($code) == $GLOBALS['TSFE']->fe_user->sesData['powermail_captcha_value'] && !empty($code)) { // if code is set and equal to session value

			// clear session
			if ($clearSession) {
				$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', '');
				$GLOBALS['TSFE']->storeSessionData();
			}

			// Set error code
			$valid = 1;
		}
		return $valid;
	}

	/**
	 * Create Image File
	 *
	 * @param $content
	 * @return string	Image HTML Code
	 */
	protected function createImage($content) {
		$subfolder = '';
		if (t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/' != t3lib_div::getIndpEnv('TYPO3_SITE_URL')) { // if request_host is different to site_url (TYPO3 runs in a subfolder)
			$subfolder = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/', '', t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // get the folder (like "subfolder/")
		}
		$startimage = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->conf['captcha.']['default.']['image']); // background image

		if (!is_file($startimage)) { // if file is correct
			return 'Error: No Image found';
		}

		$img = ImageCreateFromPNG($startimage); // Backgroundimage
		$config = array();
		$config['color_rgb'] = sscanf($this->conf['captcha.']['default.']['textColor'], '#%2x%2x%2x'); // change HEX color to RGB
		$config['color'] = ImageColorAllocate($img, $config['color_rgb'][0], $config['color_rgb'][1], $config['color_rgb'][2]); // Font color
		$config['font'] = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->conf['captcha.']['default.']['font']); // fontfile
		$config['fontsize'] = $this->conf['captcha.']['default.']['textSize']; // Fontsize
		$config['angle'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['textAngle'], 1); // give me the angles for the font
		$config['fontangle'] = mt_rand($config['angle'][0], $config['angle'][1]); // random angle
		$config['distance_hor'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['distanceHor'], 1); // give me the horizontal distances
		$config['fontdistance_hor'] = mt_rand($config['distance_hor'][0], $config['distance_hor'][1]); // random distance
		$config['distance_vert'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['distanceVer'], 1); // give me the vertical distances
		$config['fontdistance_vert'] = mt_rand($config['distance_vert'][0], $config['distance_vert'][1]); // random distance
		imagettftext($img, $config['fontsize'], $config['fontangle'], $config['fontdistance_hor'], $config['fontdistance_vert'], $config['color'], $config['font'], $content); // add text to image
		imagepng($img, t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->captchaImage)); // save image file
		imagedestroy($img); // delete temp image

		return $GLOBALS['TSFE']->tmpl->getFileName($this->captchaImage) . '?hash=' . time(); // path to new image
	}

	/**
	 * Create Random String for Captcha Image
	 *
	 * @return string
	 */
	protected function getString() {
		// config
		// 1. Get random numbers
		$op = mt_rand(0, 1); // operator +/-
		for ($i = 0; $i < 100; $i++) { // loop max. 100 times
			$number1 = mt_rand(0, 15); // random number 1
			$number2 = mt_rand(0, 15); // random number 2

			// don't want negative numbers
			if ($op != 1 || $number1 > $number2) {
				break;
			}
		}
		switch ($op) { // give me the operator
			case 0:
				$operator = '+'; // operator
				$result = $number1 + $number2; // result
				break;

			case 1:
				$operator = '-';
				$result = $number1 - $number2; // result
				break;

			case 2:
				$operator = 'x';
				$result = $number1 * $number2; // result
				break;

			case 3:
				$operator = ':';
				$result = $number1 / $number2; // result
				break;
		}

		// Save result to session
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', $result); // Generate Session with result
		$GLOBALS['TSFE']->storeSessionData(); // Save session

		return $number1 . ' ' . $operator . ' ' . $number2;
	}

}
?>