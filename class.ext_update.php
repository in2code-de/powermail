<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
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

/**
 * Class for updating powermail content elements
 *
 * @author	 Alexander Grein <ag@mediaessenz.eu>
 * @package	TYPO3
 * @subpackage tx_powermail
 */
class ext_update {
	private $storagePageEmpty = array();
	private $ll = 'LLL:EXT:powermail/locallang.xml:updater.';

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	public function main() {
		$out = '';
		if (t3lib_div::int_from_ver(TYPO3_version) < 4003000) {
			// add flash messages styles
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('powermail');
			$out .= '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'res/css/flashmessages.css" media="screen" />';
		}
		// analyze
		$this->storagePageEmpty = $this->getStoragePageEmpty();
		if (t3lib_div::_GP('do_update')) {
			$out .= '<a href="' . t3lib_div::linkThisScript(array('do_update' => '', 'func' => '')) . '">' . $GLOBALS['LANG']->sL($this->ll . 'back') . '</a><br/>';
			$func = trim(t3lib_div::_GP('func'));
			if (method_exists($this, $func)) {
				$out .= '
<div style="padding:15px 15px 20px 0;">
	<div class="typo3-message message-ok">
		<div class="message-header">' . $GLOBALS['LANG']->sL($this->ll . 'updateresults') . '</div>
		<div class="message-body">
		' . $this->$func() . '
		</div>
	</div>
</div>';
			} else {
				$out .= '
<div style="padding:15px 15px 20px 0;">
	<div class="typo3-message message-error">
		<div class="message-body">ERROR: ' . $func . '() not found</div>
	</div>
</div>';
			}
		} else {
			$out .= '<a href="' . t3lib_div::linkThisScript(array('do_update' => '', 'func' => '')) . '">' . $GLOBALS['LANG']->sL($this->ll . 'reload') . '
			<img style="vertical-align:bottom;" ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/refresh_n.gif', 'width="18" height="16"') . '></a><br/>';
			$out .= $this->displayWarning();
			$out .= '<h3>' . $GLOBALS['LANG']->sL($this->ll . 'actions') . '</h3>';
			// Update all flexform
			$out .= $this->displayUpdateOption('searchStoragePageEmpty', count($this->storagePageEmpty), 'updateStoragePageEmpty');
		}

		return $out;
	}

	/**
	 * Display the html of the update option
	 * @param string $k
	 * @param integer $count
	 * @param string $func
	 * @return hteml
	 */
	private function displayUpdateOption($k, $count, $func) {
		$msg = $GLOBALS['LANG']->sL($this->ll . 'msg_' . $k) . ' ';
		$msg .= '<br/><strong>' . str_replace('###COUNT###', $count, $GLOBALS['LANG']->sL($this->ll . 'foundMsg_' . $k)) . '</strong>';
		$msg .= ' <img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_' . ($count == 0 ? 'ok'
				: 'warning2') . '.gif', 'width="18" height="16"') . '>';
		if ($count) {
			$msg .= '<p style="margin:5px 0;">' . $GLOBALS['LANG']->sL($this->ll . 'question_' . $k) . '<p>';
			$msg .= '<p style="margin-bottom:10px;"><em>' . $GLOBALS['LANG']->sL($this->ll . 'questionInfo_' . $k) . '</em><p>';
			$msg .= $this->getButton($func);
		} else {
			$msg .= '<br/>' . $GLOBALS['LANG']->sL($this->ll . 'nothingtodo');
		}
		$out = $this->wrapForm($msg, $GLOBALS['LANG']->sL($this->ll . 'lbl_' . $k));
		$out .= '<br/><br/>';

		return $out;
	}

	/**
	 * Display the warningmessage
	 *
	 * @return html
	 */
	private function displayWarning() {
		$out = '
<div style="padding:15px 15px 20px 0;">
	<div class="typo3-message message-warning">
		<div class="message-header">' . $GLOBALS['LANG']->sL($this->ll . 'warningHeader') . '</div>
		<div class="message-body">
			' . $GLOBALS['LANG']->sL($this->ll . 'warningMsg') . '
		</div>
	</div>
</div>';

		return $out;
	}

	/**
	 * Returns the fieldset of updatesection
	 *
	 * @param string $content
	 * @param string $fsLabel
	 * @return html
	 */
	private function wrapForm($content, $fsLabel) {
		$out = '
<form action="">
	<fieldset style="background:#f4f4f4;margin-right:15px;padding:10px;">
		<legend>' . $fsLabel . '</legend>
		' . $content . '
	</fieldset>
</form>';

		return $out;
	}

	/**
	 * Return the button for update
	 *
	 * @param string $func
	 * @param string $lbl
	 * @return html
	 */
	private function getButton($func, $lbl = 'DO IT') {
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";
		$button = '<input type="submit" value="' . $lbl . '" onclick="' . htmlspecialchars($onClick) . '">';

		return $button;
	}

	/**
	 * Returns all content elements with old values
	 *
	 * @return array
	 */
	private function getStoragePageEmpty() {
		if (count($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', 'sys_log', 'type = 4 AND details LIKE \'%updateStoragePage%\'' . t3lib_BEfunc::deleteClause('sys_log')))) {
			return array();
		}
		$select_fields = '*';
		$from_table = 'tt_content';
		$where_clause = 'tx_powermail_pages=\'\' AND CType=\'powermail_pi1\'';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause);
		$resultRows = array();
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$resultRows[] = $row;
			}
		}
		if (count($resultRows) < 1) {
			// write log to ensure it will be used only once
			$GLOBALS['BE_USER']->simplelog('updateStoragePage nothing to do', 'powermail', 0);
		}

		return $resultRows;
	}

	/**
	 * Update the content elements
	 *
	 * @return string
	 */
	private function updateStoragePageEmpty() {
		$msg = null;
		if (count($this->storagePageEmpty) > 0) {
			foreach ($this->storagePageEmpty as $content) {
				// Update the content
				$table = 'tt_content';
				$where = 'uid= ' . $content['uid'];
				$fields_values = array(
					'tx_powermail_pages' => $content['pid']
				);
				if ($GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values)) {
					$msg[] = 'Updated tt_content uid: ' . $content['uid'];
				}
			}
		}
		// write log to ensure it will be used only once
		$GLOBALS['BE_USER']->simplelog('updateStoragePage successful', 'powermail', 0);

		return implode('<br/>', $msg);
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 * (this function is called from the extension manager)
	 *
	 * @param	string		$what: what should be updated
	 * @return	boolean
	 */
	public function access($what = 'all') {
		return TRUE;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/class.ext_update.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/class.ext_update.php']);
}
?>