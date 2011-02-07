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

/**
 * Plugin 'tx_powermail' for the 'powermail' extension.
 *
 * @author	Alex Kellner (alexander.kellner@in2code.de)
 * @package	TYPO3
 * @subpackage	tx_powermail_scheduler
 */

class tx_powermail_scheduler extends tx_scheduler_Task {
	
	public $lang;
	
	/**
	* Function executed from the Scheduler.
	*
	* @return    bool
	*/
	public function execute() {
		require_once(t3lib_extMgm::extPath('lang', 'lang.php')); // include lang class
		$LANG = t3lib_div::makeInstance('language');
		$LANG->init('en');

		if (intval($this->pid) === 0) {
			$this->msg = 'No Page ID given!';
			return false;
		}
		
		// tsconfig
		$tmp_defaultconfig = array (
			'time' => 86400, // default setting 1 day
			'body' => 'See XLS file in attachment', // default body
			'subject' => 'New powermail export email', // default subject
			'email_receiver' => '', // default: no receiver mail
			'email_receiver_cc' => '', // default: no cc mail
			'email_sender' => 'noreply@einpraegsam.net', // default sender address
			'sender' => 'powermail', // default sender name
			'format' => 'email_csv', // export in format email_csv or email_html or email_xls
			'attachedFilename' => '' // overwrite filename
		);
		$tmp_tsconfig = t3lib_BEfunc::getModTSconfig($this->pid, 'tx_powermail_cli'); // get whole tsconfig from backend
		$tsconfig = array_merge((array) $tmp_defaultconfig, (array) $tmp_tsconfig['properties']['exportmail.']); // overwrite from page tsconfig
		if (t3lib_div::validEmail($this->email)) {
			$tsconfig['email_receiver'] = $this->email; // overwrite from schedular settings
		}
		if (intval($this->timeframe) > 0) {
			$tsconfig['time'] = intval($this->timeframe); // overwrite from schedular settings
		}
		
		if (!t3lib_div::validEmail($tsconfig['email_receiver'])) { // if receiver email is set
			$this->msg = 'Wrong receiver mail given!';
			return false;
		}
		
		if (!t3lib_extMgm::isLoaded('phpexcel_library') && $tsconfig['format'] == 'email_xls') {
			$this->msg = 'Please use csv or install extension phpexcel_library';
			return false;
		}
		
		// Generate the xls file
		$export = t3lib_div::makeInstance('tx_powermail_export');
		$export->pid = $pid; // set page id
		$export->startDateTime = (time() - $tsconfig['time']); // set starttime
		$export->endDateTime = time(); // set endtime
		$export->export = (stristr($tsconfig['format'], 'email_') ? $tsconfig['format'] : $this->tmp_defaultconfig['format']); // set
		$export->LANG = $LANG;
		if (!empty($tsconfig['attachedFilename'])) {
			$export->overwriteFilename = $tsconfig['attachedFilename']; // overwrite filename with this
		}
		$export->main(); // generate file
		$file = t3lib_div::getFileAbsFileName('typo3temp/' . $export->filename); // read filename
		
		if (!empty($file)) { // if file is not empty
			// Generate the mail
			$htmlMail = t3lib_div::makeInstance('t3lib_htmlmail'); // New object: TYPO3 mail class
			$htmlMail->start(); // start htmlmail
			$htmlMail->recipient = $tsconfig['email_receiver']; // main receiver
			$htmlMail->recipient_copy = $tsconfig['email_receiver_cc']; // cc
			$htmlMail->subject = $tsconfig['subject']; // mail subject
			$htmlMail->from_email = $tsconfig['email_sender']; // sender email
			$htmlMail->from_name = $tsconfig['sender']; // sender name
			$htmlMail->addAttachment($file); // add attachment
			$htmlMail->addPlain($tsconfig['body']); // add plaintext
			$htmlMail->setHTML($htmlMail->encodeMsg($tsconfig['body'])); // html format if active via constants
			$htmlMail->setHeaders();
			$htmlMail->setContent();
			if ($htmlMail->sendTheMail()) {
				$this->msg = 'Mail successfully sent';
			} else {
				$this->msg = 'Powermail Error in sending mail';
			}
	
		} else {
			$content = 'There are no mails to export in the last ' . intval($tsconfig['time']) . ' seconds in pid ' . $this->pid;
		}
		
		return true;
	}
	
	/**
	* Return message for backend
	*
	* @return    bool
	*/
	public function getAdditionalInformation() {
		return $this->msg;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/cli/class.tx_powermail_scheduler.php']);
}
?>