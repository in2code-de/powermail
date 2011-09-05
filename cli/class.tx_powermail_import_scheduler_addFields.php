<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
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

// stop implementation in frontend (only for backend)
if (!interface_exists(tx_scheduler_AdditionalFieldProvider)) {
	return;
}

/**
 * Aditional fields provider class for usage with the powermail scheduler import task.
 *
 * @author	Alexander Grein (ag@mediaessenz.eu)
 * @package	TYPO3
 * @subpackage	tx_powermail_import_scheduler_addFields
 */

class tx_powermail_import_scheduler_addFields implements tx_scheduler_AdditionalFieldProvider {

	/**
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds an email field
	 *
	 * @param	array					$taskInfo: reference to the array containing the info used in the add/edit form
	 * @param	object					$task: when editing, reference to the current task object. Null when adding.
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	array					Array containg all the information pertaining to the additional fields
	 *									The array is multidimensional, keyed to the task class name and each field's id
	 *									For each field it provides an associative sub-array with the following:
	 *										['code']		=> The HTML code for the field
	 *										['label']		=> The label of the field (possibly localized)
	 *										['cshKey']		=> The CSH key for the field
	 *										['cshLabel']	=> The code of the CSH label
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
		$additionalFields = array();

		if (empty($taskInfo['fileurl'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['fileurl'] = $task->fileurl;
			} else {
				$taskInfo['fileurl'] = 'http://';
			}
		}

		if (empty($taskInfo['pid'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['pid'] = $task->pid;
			} else {
				$taskInfo['pid'] = '';
			}
		}

		if (empty($taskInfo['formuid'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['formuid'] = $task->formuid;
			} else {
				$taskInfo['formuid'] = '';
			}
		}

		if (empty($taskInfo['firstline'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['firstline'] = $task->firstline;
			} else {
				$taskInfo['firstline'] = '1';
			}
		}

		if (empty($taskInfo['delimiter'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['delimiter'] = $task->delimiter;
			} else {
				$taskInfo['delimiter'] = 'semicolon';
			}
		}

		if (empty($taskInfo['enclosure'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['enclosure'] = $task->enclosure;
			} else {
				$taskInfo['enclosure'] = 'quotation_marks_double';
			}
		}

		if (empty($taskInfo['encoding'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['encoding'] = $task->encoding;
			} else {
				$taskInfo['encoding'] = 'UTF-8';
			}
		}

		if (empty($taskInfo['connections'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['connections'] = $task->connections;
			} else {
				$taskInfo['connections'] = '';
			}
		}

		if (empty($taskInfo['limit'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['limit'] = $task->limit;
			} else {
				$taskInfo['limit'] = '';
			}
		}

		// Write the code for the fileurl field
		$fieldID = 'task_fileurl';
		$fieldCode = '<input type="text" name="tx_scheduler[fileurl]" id="' . $fieldID . '" value="' . $taskInfo['fileurl'] . '" size="55" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.fileurl')
		);

		// Write the code for the delimiter field
		$fieldID = 'task_delimiter';
		$fieldCode = '<select name="tx_scheduler[delimiter]" id="' . $fieldID . '">
            <option value="semicolon"' . ($taskInfo['delimiter'] == 'semicolon' ? ' selected="selected"'
				: '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:delimiter.0') . '</option>
            <option value="comma"' . ($taskInfo['delimiter'] == 'comma' ? ' selected="selected"'
				: '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:delimiter.1') . '</option>
            <option value="tab"' . ($taskInfo['delimiter'] == 'tab' ? ' selected="selected"' : '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:delimiter.2') . '</option>
        </select>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.delimiter')
		);

		$fieldID = 'task_enclosure';
		$fieldCode = '<select name="tx_scheduler[enclosure]" id="' . $fieldID . '">
            <option value="quotation_marks_double"' . ($taskInfo['enclosure'] == 'quotation_marks_double' ? ' selected="selected"'
				: '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:enclosure.0') . '</option>
            <option value="quotation_marks_single"' . ($taskInfo['enclosure'] == 'quotation_marks_single' ? ' selected="selected"'
				: '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:enclosure.1') . '</option>
            <option value="none"' . ($taskInfo['enclosure'] == 'none' ? ' selected="selected"'
				: '') . '>' . $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:enclosure.2') . '</option>
        </select>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.enclosure')
		);

		// Write the code for the encoding field
		$fieldID = 'task_encoding';
		$fieldCode = '<input type="text" name="tx_scheduler[encoding]" id="' . $fieldID . '" value="' . $taskInfo['encoding'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.encoding')
		);

		// Write the code for the firstline field
		$fieldID = 'task_firstline';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[firstline]" id="' . $fieldID . '" value="1"' . ($taskInfo['firstline'] == 1 ? ' checked="checked"' : '') . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.firstline')
		);

		// Write the code for the encoding field
		$fieldID = 'task_limit';
		$fieldCode = '<input type="text" name="tx_scheduler[limit]" id="' . $fieldID . '" value="' . $taskInfo['limit'] . '" size="4" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.limit')
		);

		// Write the code for the pid field
		$fieldID = 'task_pid';
		$fieldCode = '<input type="text" name="tx_scheduler[pid]" id="' . $fieldID . '" value="' . $taskInfo['pid'] . '" size="4" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.pid')
		);

		// Write the code for the formuid field
		$fieldID = 'task_formuid';
		$fieldCode = '<input type="text" name="tx_scheduler[formuid]" id="' . $fieldID . '" value="' . $taskInfo['formuid'] . '" size="4" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.formuid')
		);

		// Write the code for the connections field
		$fieldID = 'task_connections';
		$fieldCode = '<textarea name="tx_scheduler[connections]" id="' . $fieldID . '" rows="20" cols="55" />' . $taskInfo['connections'] . '</textarea>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => $GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:label.connections')
		);

		return $additionalFields;
	}

	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param	array					$submittedData: reference to the array containing the data submitted by the user
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {

		$submittedData['pid'] = trim($submittedData['pid']);
		$submittedData['formuid'] = trim($submittedData['formuid']);
		$submittedData['fileurl'] = strip_tags(trim($submittedData['fileurl']));
		$submittedData['encoding'] = strip_tags(trim($submittedData['encoding']));
		$submittedData['limit'] = trim($submittedData['limit']);

		if (empty($submittedData['pid'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noPid'), t3lib_FlashMessage::ERROR);
			return false;
		} else {
			$submittedData['pid'] = intval($submittedData['pid']); // should be integer
		}

		if (empty($submittedData['formuid'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noFormuid'), t3lib_FlashMessage::ERROR);
			return false;
		} else {
			$submittedData['formuid'] = intval($submittedData['formuid']); // should be integer
		}

		if (empty($submittedData['fileurl'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noFileurl'), t3lib_FlashMessage::ERROR);
			return false;
		}

		if (empty($submittedData['encoding'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:powermail/cli/locallang.xml:msg.noEncoding'), t3lib_FlashMessage::ERROR);
			return false;
		}

		if (!empty($submittedData['limit'])) {
			$submittedData['limit'] = intval($submittedData['limit']); // should be integer
		}

		return true;
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param	array				$submittedData: array containing the data submitted by the user
	 * @param	tx_scheduler_Task	$task: reference to the current task object
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->pid = $submittedData['pid'];
		$task->formuid = $submittedData['formuid'];
		$task->fileurl = $submittedData['fileurl'];
		$task->firstline = $submittedData['firstline'];
		$task->limit = $submittedData['limit'];
		$task->enclosure = $submittedData['enclosure'];
		$task->delimiter = $submittedData['delimiter'];
		$task->encoding = $submittedData['encoding'];
		$task->connections = $submittedData['connections'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_import_scheduler_addFields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_import_scheduler_addFields.php']);
}
?>