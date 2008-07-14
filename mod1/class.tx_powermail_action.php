<?php

class tx_powermail_action {

	// Function Main
	function main($deleteID, $LANG) {
		// config
		$this->mailID = $mailID;
		$this->LANG = $LANG;
		$this->content = '<div style="margin: 10px 0; padding: 10px; border: 1px solid #7D838C; background-color: green; color: white;"><strong>'.sprintf($this->LANG->getLL('del_message'), $deleteID).'</strong></div>';
		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery ( // deleted = 1 in db
			'tx_powermail_mails',
			'uid = '.$deleteID,
			array (
				'deleted' => 1
			)
		);
		
		return $this->content; // return message
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_action.php']);
}
?>