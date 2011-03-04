#! /usr/bin/php -q
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Alex Kellner <alexander.kellner@in2code.de>
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
require(dirname(PATH_thisScript) . '/' . $BACK_PATH.'init.php');
require_once(PATH_t3lib . 'class.t3lib_admin.php');
require_once(PATH_t3lib . 'class.t3lib_cli.php');
require_once(PATH_typo3 . 'template.php');
require_once(PATH_t3lib . 'class.t3lib_htmlmail.php');
if (t3lib_div::compat_version('4.5')){
    require_once(PATH_t3lib . 'class.t3lib_mail_message.php');
}
require_once('../mod1/class.tx_powermail_export.php'); // include div functions
require_once(t3lib_extMgm::extPath('lang', 'lang.php')); // include lang class
$LANG = t3lib_div::makeInstance('language');
$LANG->init('en');
$pid = intval($_GET['pid']);
$content = $file = '';

if ($pid > 0) { // if Page id given from GET param
	
	// tsconfig
	$tmp_defaultconfig = array (
		'time' => 86400, // default setting 1 day
		'body' => 'See attached file', // default body
		'subject' => 'New powermail export email', // default subject
		'email_receiver' => '', // default: no receiver mail
		'email_receiver_cc' => '', // default: no cc mail
		'email_sender' => 'noreply@einpraegsam.net', // default sender address
		'sender' => 'powermail', // default sender name
		'format' => 'email_csv', // export in format email_csv or email_html or email_xls
		'attachedFilename' => '' // overwrite filename
	);
	$tmp_tsconfig = t3lib_BEfunc::getModTSconfig($pid, 'tx_powermail_cli'); // get whole tsconfig from backend
	$tsconfig = array_merge((array) $tmp_defaultconfig, (array) $tmp_tsconfig['properties']['exportmail.']); // get tsconfig from powermail cli
	
	if (t3lib_div::validEmail($tsconfig['email_receiver'])) { // if receiver email is set
		
		if (t3lib_extMgm::isLoaded('phpexcel_library') || $tsconfig['format'] != 'email_xls') {

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

			if ($export->resNumRows > 0) { // if file is not empty
				
                $file = t3lib_div::getFileAbsFileName('typo3temp/' . $export->filename); // read filename
                if (t3lib_div::compat_version('4.5')){
                    // new TYPO3 swiftmailer code
                    $mail = t3lib_div::makeInstance('t3lib_mail_Message');
                    $mail->setTo(array($tsconfig['email_receiver']))
                        ->setFrom(array($tsconfig['email_sender'] => $tsconfig['sender']))
                        ->setSubject($tsconfig['subject'])
                        ->addPart($tsconfig['body'], 'text/plain')
                        ->setBody($tsconfig['body'], 'text/html')
                        ->attach(Swift_Attachment::fromPath($file))
                        ->setCharset($GLOBALS['TSFE']->metaCharset);

                    if ($tsconfig['email_receiver_cc'] !== ''){
                        $mail->setCc(t3lib_div::trimExplode(',', $tsconfig['email_receiver_cc']));
                    }
                    $mail->send();
                    $success = $mail->isSent();

                } else {
                    // Generate the mail
                    $mail = t3lib_div::makeInstance('t3lib_htmlmail'); // New object: TYPO3 mail class
                    $mail->start(); // start htmlmail
                    $mail->recipient = $tsconfig['email_receiver']; // main receiver
                    $mail->recipient_copy = $tsconfig['email_receiver_cc']; // cc
                    $mail->subject = $tsconfig['subject']; // mail subject
                    $mail->from_email = $tsconfig['email_sender']; // sender email
                    $mail->from_name = $tsconfig['sender']; // sender name
                    $mail->addAttachment($file); // add attachment
                    $mail->addPlain($tsconfig['body']); // add plaintext
                    $mail->setHTML($mail->encodeMsg($tsconfig['body'])); // html format if active via constants
                    $success = $mail->send();
                }
				if ($success) {
					$content .= 'Mail successfully sent';
				} else {
					$content .= 'Powermail Error in sending mail';
				}
                unlink($file);
		
			} else {
				$content .= 'There are no mails to export in the last ' . intval($tsconfig['time']) . ' seconds in pid ' . $pid;
			}
		} else {
			$content .= 'Please install the extension phpexcel_library or change your settings to a csv file';
		}
	} else {
		$content .= 'Powermail Error: No or invalid receiver Email address (maybe you forget to set the receiver email in the tsconfig of page ' . $pid . ')';
	}

} else {
	$content .= 'Powermail Error: No pid given (open this script with pid like ...cronjob.php?pid=1)';
}

echo $content;
?>