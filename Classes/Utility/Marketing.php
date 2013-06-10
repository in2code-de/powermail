<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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

class Tx_Powermail_Utility_Marketing {

	/**
	 * Session Prefix
	 *
	 * @var string
	 */
	public $sessionPrefix = 'powermail_marketing';

	/**
	 * Main function for additional google information
	 *
	 * @param	string		Given content (normally empty)
	 * @param	array		TypoScript configuration for this userFunc
	 * @return	void
	 */
	public function store($content = '', $conf = array()) {
		$info = array(
			'marketingSearchterm' => $this->getSearchTerm(),
			'marketingReferer' => $this->getExternalReferer(),
			'marketingPayedSearchResult' => $this->fromAdwords(),
			'marketingLanguage' => ($GLOBALS['TSFE']->tmpl->setup['config.']['sys_language_uid'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['sys_language_uid'] : 0),
			'marketingBrowserLanguage' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
			'marketingFunnel' => array(
				$GLOBALS['TSFE']->id
			)
		);

		$this->storeInSession($info);
	}

	/*
	 * Store info array into session
	 *
	 * @param	array		Info Array
	 * @return	void
	 */
	private function storeInSession($newInfo) {
		// 1. get old values
		$oldInfo = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->sessionPrefix);

		// 2. create array to store
		$info = $newInfo;
		if (is_array($oldInfo)) {
			if (!$newInfo['marketingReferer']) {
				$info['marketingReferer'] = $oldInfo['marketingReferer'];
			}
			if (!$newInfo['marketingSearchterm']) {
				$info['marketingSearchterm'] = $oldInfo['marketingSearchterm'];
			}
			if (!$newInfo['marketingPayedSearchResult']) {
				$info['marketingPayedSearchResult'] = $oldInfo['marketingPayedSearchResult'];
			}
			if (is_array($oldInfo['marketingFunnel'])) {
				$info['marketingFunnel'] = $oldInfo['marketingFunnel'];
				$info['marketingFunnel'][] = $newInfo['marketingFunnel'][0];
			}
		}

		// 3. store in session
		$GLOBALS['TSFE']->fe_user->setKey(
			'ses',
			$this->sessionPrefix,
			$info
		);
		$GLOBALS['TSFE']->storeSessionData();
	}

	/**
	 * Checks if last page was an external page
	 *
	 * return	string		URL of the last page (if different domain)
	 */
	private function getExternalReferer() {
		$url = parse_url(htmlentities(t3lib_div::getIndpEnv('HTTP_REFERER'))); // every part of the referer in an own array

		// if this domain is different to referer domain
		if (t3lib_div::getIndpEnv('HTTP_HOST') != $url['host']) {
			return t3lib_div::getIndpEnv('HTTP_REFERER');
		}
		return false;
	}

	/**
	 * Checks searchterm from last page
	 *
	 * return	string		Searchterm
	 */
	private function getSearchTerm() {
		$url = parse_url(htmlentities(t3lib_div::getIndpEnv('HTTP_REFERER'))); // every part of the referer in an own array

		if (!isset($url['query'])) { // if GET params is set
			return false;
		}

		preg_match('/q=([^&]+)(&amp;)?/', $url['query'], $output); // give me only the &q="searchword" part

		if ($output[1]) { // only if GET param &q= was set
			return urldecode($output[1]);
		}

		return false;
	}

	/**
	 * Checks if last external page was a google adwords link
	 *
	 * return	bool
	 */
	private function fromAdwords() {
		$url = parse_url(htmlentities(t3lib_div::getIndpEnv('HTTP_REFERER'))); // every part of the referer in an own array

		preg_match('/adurl=([^&]+)(&amp;)?/', $url['query'], $output); // give me only the &q="searchword" part
		if ($output[1]) {
			return 1;
		}
		return 0;
	}

	/**
	 * Function to read values from session
	 *
	 * @param	string		Given content (normally empty)
	 * @param	array		TypoScript configuration for this userFunc
	 * return	sting		Session values
	 */
	public function readSession($content = '', $conf = array()) {
		$info = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->sessionPrefix);
		$string = 'Powermail Marketing:<br />';
		if (is_array($info)) {
			$string .= t3lib_utility_Debug::viewArray($info);
		} else {
			$string .= 'Empty Session!';
		}

		return $string;
	}
}