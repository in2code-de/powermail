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
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Utility_CalculatingCaptcha {

	/**
	 * TypoScript
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * New Image Path
	 *
	 * @var string
	 */
	protected $captchaImage = 'EXT:powermail/Resources/Public/Image/captcha.png';

	/**
	 * Render Link to Captcha Image
	 */
	public function render($conf) {
		$this->conf = $conf;

		// get random string for captcha
		$string = $this->getString();

		// create image
		$content = $this->createImage($string);
		return $content;
	}

	/**
	 * Check if given code is correct
	 *
	 * @param string $code String to compare
	 * @param boolean $clearSession Flag if session should be cleared or not
	 * @return boolean
	 */
	public function validCode($code, $clearSession = TRUE) {
		$valid = FALSE;

		// if code is set and equal to session value
		if (intval($code) === $GLOBALS['TSFE']->fe_user->sesData['powermail_captcha_value'] && !empty($code)) {

			// clear session
			if ($clearSession) {
				$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', '');
				$GLOBALS['TSFE']->storeSessionData();
			}

			// Set error code
			$valid = TRUE;
		}
		return $valid;
	}

	/**
	 * Create Image File
	 *
	 * @param string $content
	 * @return string Image HTML Code
	 */
	protected function createImage($content) {
		$subfolder = '';
		// if request_host is different to site_url (TYPO3 runs in a subfolder)
		if (t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/' != t3lib_div::getIndpEnv('TYPO3_SITE_URL')) {
			$subfolder = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/', '', t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
		}
		// background image
		$startimage = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/';
		$startimage .= $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->conf['captcha.']['default.']['image']);

		if (!is_file($startimage)) {
			return 'Error: No Image found';
		}

		$img = ImageCreateFromPNG($startimage);
		$config = array();
		// change HEX color to RGB
		$config['color_rgb'] = sscanf($this->conf['captcha.']['default.']['textColor'], '#%2x%2x%2x');
		$config['color'] = ImageColorAllocate($img, $config['color_rgb'][0], $config['color_rgb'][1], $config['color_rgb'][2]);
		$config['font'] = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/';
		$config['font'] .= $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->conf['captcha.']['default.']['font']);
		$config['fontsize'] = $this->conf['captcha.']['default.']['textSize'];
		$config['angle'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['textAngle'], 1);
		$config['fontangle'] = mt_rand($config['angle'][0], $config['angle'][1]);
		$config['distance_hor'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['distanceHor'], 1);
		$config['fontdistance_hor'] = mt_rand($config['distance_hor'][0], $config['distance_hor'][1]);
		$config['distance_vert'] = t3lib_div::trimExplode(',', $this->conf['captcha.']['default.']['distanceVer'], 1);
		$config['fontdistance_vert'] = mt_rand($config['distance_vert'][0], $config['distance_vert'][1]);
		imagettftext(
			$img,
			$config['fontsize'],
			$config['fontangle'],
			$config['fontdistance_hor'],
			$config['fontdistance_vert'],
			$config['color'],
			$config['font'],
			$content
		);
		imagepng(
			$img,
			t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $subfolder . $GLOBALS['TSFE']->tmpl->getFileName($this->captchaImage)
		);
		imagedestroy($img);

		return $GLOBALS['TSFE']->tmpl->getFileName($this->captchaImage) . '?hash=' . time();
	}

	/**
	 * Create Random String for Captcha Image
	 *
	 * @return string
	 */
	protected function getString() {
		// config
		// 1. Get random numbers
		// operator +/-
		$op = mt_rand(0, 1);
		for ($i = 0; $i < 100; $i++) {
			$number1 = mt_rand(0, 15);
			$number2 = mt_rand(0, 15);

			// don't want negative numbers
			if ($op != 1 || $number1 > $number2) {
				break;
			}
		}
		switch ($op) {
			case 0:
				$operator = '+';
				$result = $number1 + $number2;
				break;

			case 1:
				$operator = '-';
				$result = $number1 - $number2;
				break;

			case 2:
				$operator = 'x';
				$result = $number1 * $number2;
				break;

			case 3:
				$operator = ':';
				$result = $number1 / $number2;
				break;

			default:
		}

		// Save result to session
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', $result);
		$GLOBALS['TSFE']->storeSessionData();

		return $number1 . ' ' . $operator . ' ' . $number2;
	}

}