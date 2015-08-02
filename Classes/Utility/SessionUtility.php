<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
 * Session utility functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SessionUtility {

	/**
	 * Extension Key
	 */
	public static $extKey = 'powermail';

	/**
	 * Save current timestamp to session
	 *
	 * @param QueryResultInterface $forms
	 * @param array $settings
	 * @return void
	 */
	public static function saveFormStartInSession($forms, array $settings) {
		$form = $forms->getFirst();
		if ($form !== NULL && self::sessionCheckEnabled($settings)) {
			/** @var TypoScriptFrontendController $typoScriptFrontendController */
			$typoScriptFrontendController = $GLOBALS['TSFE'];
			$typoScriptFrontendController->fe_user->setKey('ses', 'powermailFormstart' . $form->getUid(), time());
			$typoScriptFrontendController->storeSessionData();
		}
	}

	/**
	 * Read form rendering timestamp from session
	 *
	 * @param integer $formUid Form UID
	 * @param array $settings
	 * @return integer Timestamp
	 */
	public static function getFormStartFromSession($formUid, array $settings) {
		if (self::sessionCheckEnabled($settings)) {
			/** @var TypoScriptFrontendController $typoScriptFrontendController */
			$typoScriptFrontendController = $GLOBALS['TSFE'];
			return (int) $typoScriptFrontendController->fe_user->getKey('ses', 'powermailFormstart' . $formUid);
		}
		return 0;
	}

	/**
	 * Store Marketing Information in Session
	 *        'refererDomain' => domain.org
	 *        'referer' => http://domain.org/xyz/test.html
	 *        'country' => Germany
	 *        'mobileDevice' => 1
	 *        'frontendLanguage' => 3
	 *        'browserLanguage' => en-us
	 *        'feUser' => userAbc
	 *        'pageFunnel' => array(2, 5, 1)
	 *
	 * @param string $referer Referer
	 * @param int $language Frontend Language Uid
	 * @param int $pid Page Id
	 * @param int $mobileDevice Is mobile device?
	 * @return void
	 */
	public static function storeMarketingInformation($referer = NULL, $language = 0, $pid = 0, $mobileDevice = 0) {
		$marketingInfo = self::getSessionValue('powermail_marketing');

		// initially create array with marketing info
		if (!is_array($marketingInfo)) {
			$marketingInfo = array(
				'refererDomain' => DivUtility::getDomainFromUri($referer),
				'referer' => $referer,
				'country' => DivUtility::getCountryFromIp(),
				'mobileDevice' => $mobileDevice,
				'frontendLanguage' => $language,
				'browserLanguage' => GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
				'pageFunnel' => array($pid)
			);
		} else {
			// add current pid to funnel
			$marketingInfo['pageFunnel'][] = $pid;

			// clean pagefunnel if has more than 256 entries
			if (count($marketingInfo['pageFunnel']) > 256) {
				$marketingInfo['pageFunnel'] = array($pid);
			}
		}

		// store in session
		self::setSessionValue('powermail_marketing', $marketingInfo, TRUE);
	}

	/**
	 * Read MarketingInfos from Session
	 *
	 * @return array
	 */
	public static function getMarketingInfos() {
		$marketingInfo = self::getSessionValue('powermail_marketing');
		if (!is_array($marketingInfo)) {
			$marketingInfo = array(
				'refererDomain' => '',
				'referer' => '',
				'country' => '',
				'mobileDevice' => 0,
				'frontendLanguage' => 0,
				'browserLanguage' => '',
				'pageFunnel' => array()
			);
		}
		return $marketingInfo;
	}

	/**
	 * Save values to session for prefilling on upcoming form renderings
	 *
	 * @param Mail $mail
	 * @return void
	 */
	public static function saveSessionValuesAfterSubmit(Mail $mail) {
		// todo
	}

	/**
	 * Check if spamshield and sessioncheck is enabled
	 *
	 * @param array $settings
	 * @return bool
	 */
	protected static function sessionCheckEnabled(array $settings) {
		$settings = GeneralUtility::removeDotsFromTS($settings);
		if (!empty($settings['spamshield']['_enable']) && !empty($settings['spamshield']['indicator']['session'])) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Read a powermail session
	 *
	 * @param string $name session name
	 * @param string $key "user" or "ses"
	 * @return string Values from session
	 */
	protected static function getSessionValue($name = '', $key = 'ses') {
		/** @var TypoScriptFrontendController $typoScriptFrontendController */
		$typoScriptFrontendController = $GLOBALS['TSFE'];
		$powermailSession = $typoScriptFrontendController->fe_user->getKey($key, self::$extKey);
		if (!empty($name) && isset($powermailSession[$name])) {
			return $powermailSession[$name];
		}
		return '';
	}

	/**
	 * Set a powermail session and merge to old one
	 *
	 * @param string $name session name
	 * @param array $values values to save
	 * @param bool $overwrite Overwrite existing values
	 * @param string $key "user" or "ses"
	 * @return void
	 */
	protected static function setSessionValue($name, $values, $overwrite = FALSE, $key = 'ses') {
		if (!$overwrite) {
			$oldValues = self::getSessionValue($name);
			$values = array_merge((array) $oldValues, (array) $values);
		}
		$newValues = array(
			$name => $values
		);

		/** @var TypoScriptFrontendController $typoScriptFrontendController */
		$typoScriptFrontendController = $GLOBALS['TSFE'];
		$typoScriptFrontendController->fe_user->setKey($key, self::$extKey, $newValues);
		$typoScriptFrontendController->storeSessionData();
	}
}