#! /usr/bin/php -q
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Alex Kellner <alexander.kellner@einpraegsam.net>
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

 
 // Defining circumstances for CLI mode:
define('TYPO3_cliMode', TRUE);
define('PATH_thisScript', $_SERVER['SCRIPT_FILENAME']);
if (!PATH_thisScript) define('PATH_thisScript', $_ENV['_'] ? $_ENV['_'] : $_SERVER['_']);
require(dirname(PATH_thisScript) . '/conf.php');
require(dirname(PATH_thisScript) . '/'.$BACK_PATH.'init.php');
require_once(PATH_t3lib . 'class.t3lib_admin.php');
require_once(PATH_t3lib . 'class.t3lib_cli.php');
require_once(PATH_typo3 . 'template.php');
require_once(PATH_t3lib . 'class.t3lib_htmlmail.php');
require_once('../mod1/class.tx_powermail_export.php'); // include div functions
require_once(t3lib_extMgm::extPath('lang', 'lang.php')); // include lang class
$LANG = t3lib_div::makeInstance('language');
$LANG->init('en');
#$LANG->includeLLFile(dirname(dirname(PATH_thisScript)) . '/mod1/locallang.xml');
$pid = intval($_GET['pid']);
$content = $file = '';

if ($pid > 0) { // if Page id given from GET param
	
	// tsconfig
	$tmp_defaultconfig = array (
		'time' => 86400, // default setting 1 day
		'body' => 'See XLS file in attachment', // default body
		'subject' => 'New powermail export email', // default subject
		'email_receiver' => '', // default: no receiver mail
		'email_receiver_cc' => '', // default: no cc mail
		'email_sender' => 'noreply@einpraegsam.net', // default sender address
		'sender' => 'powermail' // default sender name
	);
	$tmp_tsconfig = t3lib_BEfunc::getModTSconfig($pid, 'tx_powermail_cli'); // get whole tsconfig from backend
	$tsconfig = array_merge((array) $tmp_defaultconfig, (array) $tmp_tsconfig['properties']['exportmail.']); // get tsconfig from powermail cli
	
	if (t3lib_div::validEmail($tsconfig['email_receiver'])) { // if receiver email is set

		// Generate the xls file
		$export = t3lib_div::makeInstance('tx_powermail_export');
		$export->default_start = strftime('%Y-%m-%d %H:%M', (time() - $tsconfig['time'])); // current time minus delta
		$export->default_end = strftime('%Y-%m-%d %H:%M', time()); // current time (like 2010-01-01 00:00)
		$file = t3lib_div::getFileAbsFileName($export->main('email', $pid, $LANG));
		
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
				$content .= 'Mail successfully sent';
			} else {
				$content .= 'Powermail Error in sending mail';
			}
	
		} else {
			$content .= 'There are no mails to export in the last ' . $tsconfig['time'] . ' seconds in pid ' . $pid;
		}
	} else {
		$content .= 'Powermail Error: No or invalid receiver Email address (maybe you forget to set the receiver email in the tsconfig of page ' . $pid . ')';
	}

} else {
	$content .= 'Powermail Error: No pid given (open this script with pid like ...cronjob.php?pid=1)';
}

echo $content;
?>