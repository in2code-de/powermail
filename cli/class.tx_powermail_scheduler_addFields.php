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
	* @return    bool
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
		
        if (empty($taskInfo['email'])) {
            if ($parentObject->CMD == 'edit') {
                $taskInfo['email'] = $task->email;
            } else {
                $taskInfo['email'] = '';
            }
        }
		
        if (empty($taskInfo['timeframe'])) {
            if ($parentObject->CMD == 'edit') {
                $taskInfo['timeframe'] = $task->timeframe;
            } else {
                $taskInfo['timeframe'] = '';
            }
        }

        // Write the code for the pid field
        $fieldID = 'task_pid';
        $fieldCode = '<input type="text" name="tx_scheduler[pid]" id="' . $fieldID . '" value="' . $taskInfo['pid'] . '" />';
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Page ID with powermails'
        );

        // Write the code for the email field
        $fieldID = 'task_email';
        $fieldCode = '<input type="text" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" />';
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Email receiver'
        );

        // Write the code for the timeframe field
        $fieldID = 'task_timeframe';
        $fieldCode = '<input type="text" name="tx_scheduler[timeframe]" id="' . $fieldID . '" value="' . ($taskInfo['timeframe'] == '' ? '86400' : $taskInfo['timeframe']) . '" />';
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Timeframe in seconds'
        );

        return $additionalFields;
    }

	/**
	* Validate user values
	*
	* @return    bool
	*/
    public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
        $submittedData['pid'] = intval($submittedData['pid']); // should be integer
        $submittedData['timeframe'] = intval($submittedData['timeframe']); // should be integer
        if (!t3lib_div::validEmail($submittedData['email'])) { // should be a valid email address
			$submittedData['email'] = ''; // clean
		}
        return true;
    }
   
	/**
	* make values available in scheduler object
	*
	* @return    void
	*/
    public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
        $task->pid = $submittedData['pid'];
        $task->email = $submittedData['email'];
        $task->timeframe = $submittedData['timeframe'];
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler_addFields.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler_addFields.php']);
}
?>