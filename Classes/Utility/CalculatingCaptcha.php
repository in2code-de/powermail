<?php
namespace In2code\Powermail\Utility;

use \TYPO3\CMS\Core\Utility\GeneralUtility,
	\In2code\Powermail\Utility\Div;

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
 * CalculatingCaptcha
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class CalculatingCaptcha {

	/**
	 * TypoScript
	 *
	 * @var \array
	 */
	protected $configuration;

	/**
	 * Path to captcha image
	 *
	 * @var \string
	 */
	protected $captchaImage = 'typo3temp/tx_powermail/CalculatingCaptcha.png';

	/**
	 * Render Link to Captcha Image
	 *
	 * @return string
	 */
	public function render() {
		$string = $this->getStringForCaptcha();
		if (
			!is_dir(dirname($this->getCaptchaImage())) &&
			!GeneralUtility::mkdir(
				GeneralUtility::getFileAbsFileName(dirname($this->getCaptchaImage()))
			)
		) {
			return 'Error: Folder ' . dirname($this->getCaptchaImage()) . '/ don\'t exists';
		}
		return $this->createImage($string);
	}

	/**
	 * Check if given code is correct
	 *
	 * @param string $code String to compare
	 * @param bool $clearSession
	 * @return boolean
	 */
	public function validCode($code, $clearSession = TRUE) {
		if (intval($code) == $GLOBALS['TSFE']->fe_user->sesData['powermail_captcha_value'] && !empty($code)) {
			if ($clearSession) {
				$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', '');
				$GLOBALS['TSFE']->storeSessionData();
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Create Image File
	 *
	 * @param $content
	 * @return string	Image HTML Code
	 */
	protected function createImage($content) {
		$startimage = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . Div::getSubFolderOfCurrentUrl();
		$startimage .= $GLOBALS['TSFE']->tmpl->getFileName($this->configuration['captcha.']['default.']['image']);

		// if startfile does not exist
		if (!is_file($startimage)) {
			return 'Error: No Image found on ' . $startimage;
		}

		// Backgroundimage
		$img = ImageCreateFromPNG($startimage);
		$config = array();
		$config['color_rgb'] = sscanf($this->configuration['captcha.']['default.']['textColor'], '#%2x%2x%2x');
		$config['color'] = ImageColorAllocate($img, $config['color_rgb'][0], $config['color_rgb'][1], $config['color_rgb'][2]);
		$config['font'] = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . Div::getSubFolderOfCurrentUrl();
		$config['font'] .= $GLOBALS['TSFE']->tmpl->getFileName($this->configuration['captcha.']['default.']['font']);
		$config['fontsize'] = $this->configuration['captcha.']['default.']['textSize'];
		$config['angle'] = GeneralUtility::trimExplode(',', $this->configuration['captcha.']['default.']['textAngle'], TRUE);
		$config['fontangle'] = mt_rand($config['angle'][0], $config['angle'][1]);
		$config['distance_hor'] = GeneralUtility::trimExplode(',', $this->configuration['captcha.']['default.']['distanceHor'], TRUE);
		$config['fontdistance_hor'] = mt_rand($config['distance_hor'][0], $config['distance_hor'][1]);
		$config['distance_vert'] = GeneralUtility::trimExplode(',', $this->configuration['captcha.']['default.']['distanceVer'], TRUE);
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
			GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' .
				Div::getSubFolderOfCurrentUrl() . $GLOBALS['TSFE']->tmpl->getFileName($this->captchaImage)
		);
		imagedestroy($img);

		return $GLOBALS['TSFE']->tmpl->getFileName($this->getCaptchaImage()) . '?hash=' . time();
	}

	/**
	 * Create Random String for Captcha Image
	 *
	 * @return string
	 */
	protected function getStringForCaptcha() {
		$operatorNumber = mt_rand(0, 1);
		$number1 = $number2 = 0;
		for ($i = 0; $i < 100; $i++) {
			$number1 = mt_rand(0, 15);
			$number2 = mt_rand(0, 15);

			// don't want negative numbers
			if ($operatorNumber !== 1 || $number1 > $number2) {
				break;
			}
		}

		if (!empty($this->configuration['captcha.']['default.']['forceValue'])) {
			$parts = GeneralUtility::trimExplode('+', $this->configuration['captcha.']['default.']['forceValue'], TRUE);
			if (count($parts) === 2) {
				$operatorNumber = 0;
				$number1 = $parts[0];
				$number2 = $parts[1];
			}
		}

		switch ($operatorNumber) {
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

			case 0:
			default:
				$operator = '+';
				$result = $number1 + $number2;
		}

		$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_captcha_value', $result);
		$GLOBALS['TSFE']->storeSessionData();

		return $number1 . ' ' . $operator . ' ' . $number2;
	}

	/**
	 * @param array $configuration
	 * @return void
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param string $captchaImage
	 * @return void
	 */
	public function setCaptchaImage($captchaImage) {
		$this->captchaImage = $captchaImage;
	}

	/**
	 * @return string
	 */
	public function getCaptchaImage() {
		return $this->captchaImage;
	}
}