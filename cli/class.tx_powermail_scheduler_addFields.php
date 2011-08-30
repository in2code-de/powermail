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
 * Plugin 'tx_powermail' for the 'powermail' extension.
 *
 * @author	Alex Kellner (alexander.kellner@in2code.de)
 * @package	TYPO3
 * @subpackage	tx_powermail_scheduler_addFields
 */

class tx_powermail_scheduler_addFields implements tx_scheduler_AdditionalFieldProvider {

	/**
	 * Add additional fields to the scheduler
	 *
	 * @return	array
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
		$additionalFields = array();

		if (empty($taskInfo['pid'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['pid'] = $task->pid;
			} else {
				$taskInfo['pid'] = '';
			}
		}

		if (empty($taskInfo['filename'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['filename'] = $task->filename;
			} else {
				$taskInfo['filename'] = '';
			}
		}

		if (empty($taskInfo['email'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['email'] = $task->email;
			} else {
				$taskInfo['email'] = '';
			}
		}

		if (empty($taskInfo['email_sender'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['email_sender'] = $task->email_sender;
			} else {
				$taskInfo['email_sender'] = '';
			}
		}

		if (empty($taskInfo['sender'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['sender'] = $task->sender;
			} else {
				$taskInfo['sender'] = '';
			}
		}

		if (empty($taskInfo['subject'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['subject'] = $task->subject;
			} else {
				$taskInfo['subject'] = '';
			}
		}

		if (empty($taskInfo['body'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['body'] = $task->body;
			} else {
				$taskInfo['body'] = '';
			}
		}

		if (empty($taskInfo['timeframe'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['timeframe'] = $task->timeframe;
			} else {
				$taskInfo['timeframe'] = '';
			}
		}

		if (empty($taskInfo['format'])) {
			if ($parentObject->CMD == 'edit') {
				$taskInfo['format'] = $task->format;
			} else {
				$taskInfo['format'] = '';
			}
		}

		// Write the code for the pid field
		$fieldID = 'task_pid';
		$fieldCode = '<input type="text" name="tx_scheduler[pid]" id="' . $fieldID . '" value="' . $taskInfo['pid'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Page ID with powermails'
		);

		// Write the code for the filename field
		$fieldID = 'task_filename';
		$fieldCode = '<input type="text" name="tx_scheduler[filename]" id="' . $fieldID . '" value="' . $taskInfo['filename'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Filename for export without extension (leave empty for automatic generated filename)'
		);

		// Write the code for the email field
		$fieldID = 'task_email';
		$fieldCode = '<input type="text" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Email receiver'
		);

		// Write the code for the email_sender field
		$fieldID = 'task_email_sender';
		$fieldCode = '<input type="text" name="tx_scheduler[email_sender]" id="' . $fieldID . '" value="' . $taskInfo['email_sender'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Email sender'
		);

		// Write the code for the sender field
		$fieldID = 'task_sender';
		$fieldCode = '<input type="text" name="tx_scheduler[sender]" id="' . $fieldID . '" value="' . $taskInfo['sender'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Sender Name'
		);

		// Write the code for the subject field
		$fieldID = 'task_subject';
		$fieldCode = '<input type="text" name="tx_scheduler[subject]" id="' . $fieldID . '" value="' . $taskInfo['subject'] . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Email subject'
		);

		// Write the code for the body field
		$fieldID = 'task_body';
		$fieldCode = '<textarea name="tx_scheduler[body]" id="' . $fieldID . '" cols="30" rows="5">' . $taskInfo['body'] . '</textarea>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Email body'
		);

		// Write the code for the timeframe field
		$fieldID = 'task_timeframe';
		$fieldCode = '<input type="text" name="tx_scheduler[timeframe]" id="' . $fieldID . '" value="' . ($taskInfo['timeframe'] == '' ? '86400' : $taskInfo['timeframe']) . '" />';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Timeframe in seconds'
		);

		// Write the code for the export format field
		$fieldID = 'task_format';
		$fieldCode = '<select name="tx_scheduler[format]" id="' . $fieldID . '">
            <option value="email_csv"' . ($taskInfo['format'] == 'email_csv' ? ' selected="selected"' : '') . '>CSV</option>
            <option value="email_xls"' . ($taskInfo['format'] == 'email_xls' ? ' selected="selected"' : '') . '>XLS</option>
            <option value="email_html"' . ($taskInfo['format'] == 'email_html' ? ' selected="selected"' : '') . '>HTML</option>
        </select>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'Export format'
		);

		return $additionalFields;
	}

	/**
	 * Validate user values
	 *
	 * @return	bool
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$submittedData['pid'] = intval($submittedData['pid']); // should be integer
		$submittedData['filename'] = trim($submittedData['filename']); // should be integer
		$submittedData['timeframe'] = intval($submittedData['timeframe']); // should be integer
		if (!t3lib_div::validEmail($submittedData['email'])) { // should be a valid email address
			$submittedData['email'] = ''; // clean
		}
		if (!t3lib_div::validEmail($submittedData['email_sender'])) { // should be a valid email address
			$submittedData['email_sender'] = ''; // clean
		}
		return true;
	}

	/**
	 * make values available in scheduler object
	 *
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->pid = $submittedData['pid'];
		$task->filename = $submittedData['filename'];
		$task->email = $submittedData['email'];
		$task->email_sender = $submittedData['email_sender'];
		$task->sender = $submittedData['sender'];
		$task->subject = $submittedData['subject'];
		$task->body = $submittedData['body'];
		$task->timeframe = $submittedData['timeframe'];
		$task->format = $submittedData['format'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler_addFields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler_addFields.php']);
}
?>