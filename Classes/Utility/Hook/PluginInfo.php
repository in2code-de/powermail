<?php
namespace In2code\Powermail\Utility\Hook;

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
 * Show Plugin Info below Plugin
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class PluginInfo {

	/**
	 * Params
	 *
	 * @var array
	 */
	public $params;

	/**
	 * showTable
	 *
	 * @var bool
	 */
	public $showTable = TRUE;

	/**
	 * Path to locallang file
	 *
	 * @var string
	 */
	protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:pluginInfo.';

	/**
	 * Main Function
	 *
	 * @param array $params
	 * @param object $pObj
	 * @return string
	 */
	public function getInfo($params = array(), $pObj) {
		// settings
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if ($confArr['disablePluginInformation']) {
			return '';
		}
		$this->params = $params;
//		$returnUrl = 'alt_doc.php?edit[tt_content][' . $pa['row']['uid'] . ']=edit&returnUrl=' . GeneralUtility::_GET('returnUrl');
//		$returnUrl = rawurlencode($returnUrl);
		$returnUrl = rawurlencode(
			Div::getSubFolderOfCurrentUrl() . GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT')
		);
		$editFormLink = Div::getSubFolderOfCurrentUrl() . 'typo3/alt_doc.php?edit[tx_powermail_domain_model_forms][' . $this->getFormProperty($this->getFieldFromFlexform('main', 'settings.flexform.main.form'), 'uid') . ']=edit';
		$editFormLink .= '&returnUrl=' . $returnUrl;

		// let's go
		$array = array(
			$GLOBALS['LANG']->sL($this->locallangPath . 'receiverEmail') =>
				$this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.email'),
			$GLOBALS['LANG']->sL($this->locallangPath . 'receiverName') =>
				$this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.name'),
			$GLOBALS['LANG']->sL($this->locallangPath . 'subject') =>
				$this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.subject'),
			$GLOBALS['LANG']->sL($this->locallangPath . 'form') =>
				'<a href="' . $editFormLink . '" style="text-decoration:underline;">' .
				$this->getFormProperty($this->getFieldFromFlexform('main', 'settings.flexform.main.form')) .
				'</a>',
			$GLOBALS['LANG']->sL($this->locallangPath . 'confirmationPage') =>
				'<img src="' . Div::getSubFolderOfCurrentUrl() . 'typo3conf/ext/powermail/Resources/Public/Image/icon-check.png" alt="1" />',
			$GLOBALS['LANG']->sL($this->locallangPath . 'optin') =>
				'<img src="' . Div::getSubFolderOfCurrentUrl() . 'typo3conf/ext/powermail/Resources/Public/Image/icon-check.png" alt="1" />'
		);
		if (!$this->getFieldFromFlexform('main', 'settings.flexform.main.confirmation')) {
			$array[$GLOBALS['LANG']->sL($this->locallangPath . 'confirmationPage')] =
				'<img src="' . Div::getSubFolderOfCurrentUrl() .
					'typo3conf/ext/powermail/Resources/Public/Image/icon-notchecked.png" alt="0" />';
		}
		if (!$this->getFieldFromFlexform('main', 'settings.flexform.main.optin')) {
			$array[$GLOBALS['LANG']->sL($this->locallangPath . 'optin')] =
				'<img src="' . Div::getSubFolderOfCurrentUrl() .
					'typo3conf/ext/powermail/Resources/Public/Image/icon-notchecked.png" alt="0" />';
		}
		if ($this->showTable) {
			return $this->createOutput($array);
		}
		return '';
	}

	/**
	 * Get form property from uid
	 *
	 * @param string $field
	 * @param int $uid Form uid
	 * @return string
	 */
	protected function getFormProperty($uid, $field = 'title') {
		$select = $field;
		$from = 'tx_powermail_domain_model_forms';
		$where = 'uid=' . intval($uid);
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow($select, $from, $where);
		return $row[$field];
	}

	/**
	 * Create HTML Output
	 *
	 * @param array $array Values to show
	 * @return string
	 */
	protected function createOutput($array) {
		$i = 0;
		$content = '';
		$content .= '<tr class="bgColor2">';
		$content .= '<td><strong>Settings</strong></td>';
		$content .= '<td><strong>Value</strong></td>';
		$content .= '</tr>';
		foreach ($array as $key => $value) {
			$content .= '<tr class="bgColor' . ($i % 2 ? '1' : '4') . '">';
			$content .= '<td width="40%">' . $key . '</td>';
			$content .= '<td>' . $value . '</td>';
			$content .= '</tr>';
			$i++;
		}
		return '<table class="typo3-dblist">' . $content . '</table>';
	}

	/**
	 * Get field value from flexform configuration
	 *
	 * @param string $sheet name of the sheet
	 * @param string $key name of the key
	 * @return string value if found
	 */
	protected function getFieldFromFlexform($sheet, $key) {
		$flexform = GeneralUtility::xml2array($this->params['row']['pi_flexform']);

		if (
			is_array($flexform)
			&& is_array($flexform['data'][$sheet])
			&& is_array($flexform['data'][$sheet]['lDEF'])
			&& is_array($flexform['data'][$sheet]['lDEF'][$key])
			&& isset($flexform['data'][$sheet]['lDEF'][$key]['vDEF'])
		) {
			return $flexform['data'][$sheet]['lDEF'][$key]['vDEF'];
		}

		$this->showTable = FALSE;
		return FALSE;
	}
}