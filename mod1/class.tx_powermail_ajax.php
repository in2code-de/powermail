<?php 

/***************************************************************
* Copyright notice
*
* (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
* All rights reserved
*
* This script is part of the powermail project. The powermail project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This is a file of the powermail project.
 * http://forge.typo3.org/projects/show/extension-powermail
 */

/**
 * Ajax methods which are used as ajaxID-methods
 * by the powermail backend-module.
 *
 * @author Alexander Grein <ag@mediaessenz.eu>
 *
 * @package TYPO3
 * @subpackage powermail
 */
class tx_powermail_Ajax {
	
	public function ajaxController($params, $ajaxObj) {
		$cmd = t3lib_div::_GP('cmd');
		switch ($cmd) {
			case 'getItems':
				// get list of Powermails on the selected page
				$this->ajaxGetItems($params, $ajaxObj);
				break;
			case 'getLabelsAndFormtypes':
				// get labels of Powermail fields
				$this->ajaxGetLabelsAndFormtypes($params, $ajaxObj);
				break;
			case 'getItemDetails':
				// get details of Powermail
				break;
			case 'doDelete':
				// delete Item(s)
				$this->ajaxDeleteItem($params, $ajaxObj);
				break;
			case 'getExcel':
				$this->excelExport = t3lib_div::makeInstance('tx_powermail_export');
				$this->excelExport->pid = t3lib_div::_GP('pid');
				$this->excelExport->startDateTime = t3lib_div::_GP('startDateTime');
				$this->excelExport->endDateTime = t3lib_div::_GP('endDateTime');
				$this->excelExport->export = 'xls';
				echo $this->excelExport->main();
				break;
			case 'getCsv':
				$this->excelExport = t3lib_div::makeInstance('tx_powermail_export');
				$this->excelExport->pid = t3lib_div::_GP('pid');
				$this->excelExport->startDateTime = t3lib_div::_GP('startDateTime');
				$this->excelExport->endDateTime = t3lib_div::_GP('endDateTime');
				$this->excelExport->export = 'csv';
				echo $this->excelExport->main();
				break;
			case 'getHtml':
				$this->excelExport = t3lib_div::makeInstance('tx_powermail_export');
				$this->excelExport->pid = t3lib_div::_GP('pid');
				$this->excelExport->startDateTime = t3lib_div::_GP('startDateTime');
				$this->excelExport->endDateTime = t3lib_div::_GP('endDateTime');
				$this->excelExport->export = 'html';
				echo $this->excelExport->main();
				break;
			case 'getPdf':
				$this->excelExport = t3lib_div::makeInstance('tx_powermail_export');
				$this->excelExport->pid = t3lib_div::_GP('pid');
				$this->excelExport->startDateTime = t3lib_div::_GP('startDateTime');
				$this->excelExport->endDateTime = t3lib_div::_GP('endDateTime');
				$this->excelExport->export = 'pdf';
				echo $this->excelExport->main();
				break;
			default:
				// No action specified
		}
	}
	
	private function ajaxGetItems($params, $ajaxObj) {
		$this->belist = t3lib_div::makeInstance('tx_powermail_repository');
		$this->belist->pid = intval(t3lib_div::_GP('pid'));
		$this->belist->pointer = intval(t3lib_div::_GP('start'));
		$this->belist->perpage = intval(t3lib_div::_GP('pagingSize'));
		$this->belist->sort = (!t3lib_div::inArray(array('crdate', 'uid', 'sender', 'recipient', 'senderIP'), t3lib_div::_GP('sort'))) ? 'crdate' : t3lib_div::_GP('sort');
		$this->belist->dir = (t3lib_div::_GP('dir') == 'ASC') ? 'ASC' : 'DESC';
		$this->belist->startDateTime = intval(t3lib_div::_GP('startDateTime'));
		$this->belist->endDateTime = intval(t3lib_div::_GP('endDateTime'));
		$ajaxObj->setContent($this->belist->main());
		$ajaxObj->setContentFormat('jsonbody');
	}
	
	private function ajaxGetLabelsAndFormtypes($params, $ajaxObj) {
		$this->labelsAndFormtypes = t3lib_div::makeInstance('tx_powermail_repository');
		$this->labelsAndFormtypes->pid = intval(t3lib_div::_GP('pid'));
		$ajaxObj->setContent($this->labelsAndFormtypes->getLabelsAndFormtypes());
		$ajaxObj->setContentFormat('jsonbody');
	}

	private function ajaxDeleteItem($params, $ajaxObj) {
		$uids = t3lib_div::_GP('uids');
		$this->action = t3lib_div::makeInstance('tx_powermail_action');
		$this->ajaxReturn = $this->action->deleteItem($uids);
		$ajaxObj->setContent(array($this->ajaxReturn));
		$ajaxObj->setContentFormat('plain');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_ajax.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_ajax.php']);
}
?>