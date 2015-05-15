<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $typoScriptFrontendController;

	/**
	 * Operators
	 *
	 * @var array
	 */
	protected $operators = array(
		'+',
		'-',
		'x',
		':'
	);

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
		$captchaValue = $this->getStringForCaptcha();
		if (
			!is_dir(dirname($this->getCaptchaImage())) &&
			!GeneralUtility::mkdir(
				GeneralUtility::getFileAbsFileName(dirname($this->getCaptchaImage()))
			)
		) {
			return 'Error: Folder ' . dirname($this->getCaptchaImage()) . '/ don\'t exists';
		}
		$this->setCaptchaSession($captchaValue[0]);
		return $this->createImage($captchaValue[1]);
	}

	/**
	 * Check if given code is correct
	 *
	 * @param string $code String to compare
	 * @param bool $clearSession
	 * @return boolean
	 */
	public function validCode($code, $clearSession = TRUE) {
		if ((int) $code === $this->getCaptchaSession() && !empty($code)) {
			if ($clearSession) {
				$this->setCaptchaSession('');
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function getFilename($string) {
		$string = str_replace('EXT:', 'typo3conf/ext/', $string);
		/** @var \TYPO3\CMS\Core\TypoScript\TemplateService $templateService */
		$templateService = GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\TemplateService');
		return $templateService->getFileName($string);
	}

	/**
	 * Create Image File
	 *
	 * @param string $content
	 * @param bool $addHash
	 * @return string Image URI
	 */
	protected function createImage($content, $addHash = TRUE) {
		$startimage = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . Div::getSubFolderOfCurrentUrl();
		$startimage .= $this->getFileName($this->configuration['captcha.']['default.']['image']);

		if (!is_file($startimage)) {
			return 'Error: No Image found on ' . $startimage;
		}

		$img = ImageCreateFromPNG($startimage);
		$config = array();
		$config['color_rgb'] = sscanf($this->configuration['captcha.']['default.']['textColor'], '#%2x%2x%2x');
		$config['color'] = ImageColorAllocate($img, $config['color_rgb'][0], $config['color_rgb'][1], $config['color_rgb'][2]);
		$config['font'] = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . Div::getSubFolderOfCurrentUrl();
		$config['font'] .= $this->getFileName($this->configuration['captcha.']['default.']['font']);
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
			Div::getSubFolderOfCurrentUrl() . $this->captchaImage
		);
		imagedestroy($img);

		$imageUri = $this->getCaptchaImage();
		if ($addHash) {
			$imageUri .= '?hash=' . time();
		}
		return $imageUri;
	}

	/**
	 * Create Random String for Captcha Image
	 *
	 * @param int $maxNumber
	 * @param int $maxOperatorNumber choose which operators are allowd
	 * @return array
	 * 		0 => 3
	 * 		1 => '1+2'
	 */
	protected function getStringForCaptcha($maxNumber = 15, $maxOperatorNumber = 1) {
		$result = $number1 = $number2 = 0;
		$operator = $this->operators[mt_rand(0, $maxOperatorNumber)];
		for ($i = 0; $i < 100; $i++) {
			$number1 = mt_rand(0, $maxNumber);
			$number2 = mt_rand(0, $maxNumber);
			$result = $this->mathematicOperation($number1, $number2, $operator);
			if ($result > 0) {
				break;
			}
		}

		// Force values for testing
		if (!empty($this->configuration['captcha.']['default.']['forceValue'])) {
			preg_match_all('~(\d+)\s*([+|\-|:|x])\s*(\d+)~', $this->configuration['captcha.']['default.']['forceValue'], $matches);
			$number1 = $matches[1][0];
			$number2 = $matches[3][0];
			$operator = $matches[2][0];
			$result = $this->mathematicOperation($number1, $number2, $operator);
		}

		return array($result, $number1 . ' ' . $operator . ' ' . $number2);
	}

	/**
	 * Mathematic operation
	 *
	 * @param int $number1
	 * @param int $number2
	 * @param string $operator +|-|x|:
	 * @return int
	 */
	protected function mathematicOperation($number1, $number2, $operator = '+') {
		switch ($operator) {
			case '-':
				$result = $number1 - $number2;
				break;
			case 'x':
				$result = $number1 * $number2;
				break;
			case ':':
				$result = $number1 / $number2;
				break;
			case '+':
			default:
				$result = $number1 + $number2;
		}
		return $result;
	}

	/**
	 * @param string $result
	 * @return void
	 */
	protected function setCaptchaSession($result) {
		$this->typoScriptFrontendController->fe_user->setKey('ses', 'powermail_captcha_value', $result);
		$this->typoScriptFrontendController->storeSessionData();
	}

	/**
	 * @return int
	 */
	protected function getCaptchaSession() {
		return (int) $this->typoScriptFrontendController->fe_user->sesData['powermail_captcha_value'];
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

	/**
	 * Init
	 */
	public function __construct() {
		$this->typoScriptFrontendController = $GLOBALS['TSFE'];
	}
}