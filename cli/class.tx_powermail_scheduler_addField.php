<?php
class tx_powermail_scheduler_addField implements tx_scheduler_AdditionalFieldProvider {
   
    public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
    
        if (empty($taskInfo['ip'])) {
            if($parentObject->CMD == 'edit') {
                $taskInfo['ip'] = $task->ip;
            } else {
                $taskInfo['ip'] = '';
            }
        }

        if (empty($taskInfo['port'])) {
            if($parentObject->CMD == 'add') {
                $taskInfo['port'] = '80';
            } elseif($parentObject->CMD == 'edit') {
                $taskInfo['port'] = $task->port;
            } else {
                $taskInfo['port'] = '';
            }
        }

        // Write the code for the field
        $fieldID = 'task_ip';
        $fieldCode = '<input type="text" name="tx_scheduler[ip]" id="' . $fieldID . '" value="' . $taskInfo['ip'] . '" size="30" />';
        $additionalFields = array();
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'IP-Adresse/Webseite'
        );

        // Write the code for the field
        $fieldID = 'task_port';
        $fieldCode = '<input type="text" name="tx_scheduler[port]" id="' . $fieldID . '" value="' . $taskInfo['port'] . '" size="30" />';
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Port'
        );

        return $additionalFields;
    }

    public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
        $submittedData['ip'] = trim($submittedData['ip']);
        $submittedData['port'] = trim($submittedData['port']);
        return true;
    }

    public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
        $task->ip = $submittedData['ip'];
        $task->port = $submittedData['port'];
    }
}
?>