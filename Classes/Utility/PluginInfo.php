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
 * Show Plugin Info below Plugin
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Utility_PluginInfo {
	
	/**
	 * Params
	 */
	public $params;

	/**
	 * showTable
	 */
	public $showTable = 1;

	/**
	 * Main Function
	 *
	 * @param array $params
	 * @param $pObj
	 * @return string
	 */
	public function getInfo($params = array(), $pObj) {
		// settings
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if ($confArr['disablePluginInformation']) {
			return;
		}
		$this->params = $params;

		// let's go
		$array = array(
			'Empfänger E-Mail' => $this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.email'),
			'Empfänger Name' => $this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.name'),
			'Betreff' => $this->getFieldFromFlexform('receiver', 'settings.flexform.receiver.subject'),
			'Formular' => $this->getFieldFromFlexform('main', 'settings.flexform.main.form'),
			'Bestätigungsseite' => $this->getFieldFromFlexform('main', 'settings.flexform.main.confirmation') ?
				'<img src="/typo3conf/ext/powermail/Resources/Public/Image/icon-check.png" alt="1" />' :
				'<img src="/typo3conf/ext/powermail/Resources/Public/Image/icon-notchecked.png" alt="0" />',
		);
		if ($this->showTable) {
			return $this->createOutput($array);
		}
	}

	/**
	 * Create HTML Output
	 *
	 * @param $array	Values to show
	 */
	private function createOutput($array) {
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
	 * @param 	string 		$sheet name of the sheet
	 * @param 	string 		$key name of the key
	 * @return 	string		value if found
	 */
	private function getFieldFromFlexform($sheet, $key) {
		$flexform = t3lib_div::xml2array($this->params['row']['pi_flexform']);

		if (
			is_array($flexform)
			&& is_array($flexform['data'][$sheet])
			&& is_array($flexform['data'][$sheet]['lDEF'])
			&& is_array($flexform['data'][$sheet]['lDEF'][$key])
			&& isset($flexform['data'][$sheet]['lDEF'][$key]['vDEF'])
		) {
			return $flexform['data'][$sheet]['lDEF'][$key]['vDEF'];
		}

		$this->showTable = 0;
	}
}
?>