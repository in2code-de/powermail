<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Alexander Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_markers.php'); // file for marker functions
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_db.php'); // file for marker functions
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_dynamicmarkers.php'); // file for dynamicmarker functions


class tx_powermail_submit extends tslib_pibase {
	var $prefixId      = 'tx_powermail_submit';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_submit.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;
	var $email_send = 1; // Enable email send function (disable for testing only)
	var $dbInsert = 1; // Enable db insert of every sent item (disable for testing only)
	var $ok = 0; // disallow sending (standard false)
	var $PM_SubmitBeforeMarkerHook_return;
    var $useSwiftMailer = false;

	function main($conf, $sessionfields, $cObj) {
		$this->conf = $conf;
		$this->sessionfields = $sessionfields;
		$this->cObj = $cObj;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin

		// Instances
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->dbImport = t3lib_div::makeInstance('tx_powermail_db'); // New object: For additional db import (if wanted)
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_powermail_dynamicmarkers'); // New object: TYPO3 dynamicmarker function
		$this->markers = t3lib_div::makeInstance('tx_powermail_markers'); // New object: TYPO3 mail functions
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]); // Get config from localconf.php
		
		// Configuration
		$this->noReplyEmail = str_replace('###DOMAIN###', str_replace(array('www.','www1.','www2.','www3.','www4.','www5.'), '', $_SERVER['SERVER_NAME']), $this->conf['email.']['noreply']); // no reply email address from TS setup
		$this->sessiondata = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey . '_' . ($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'])); // Get piVars from session
		$this->emailSettings(); // emailSettings
		$this->markerArray = array();
		
		// Templates
		$this->tmpl = array();
		$this->mailcontent = array();
		$this->tmpl['thx'] = $this->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['thxMessage']), '###POWERMAIL_THX###'); // Load HTML Template: THX (works on subpart ###POWERMAIL_THX###)
		$this->tmpl['all'] = $this->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['all']), '###POWERMAIL_ALL###'); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->tmpl['emails']['all'] = tslib_cObj::fileResource($this->conf['template.']['emails']); // Load HTML Template: Emails
		
		
		// 1. add hook for manipulation of data after E-Mails where sent
		$submitBeforeEmailsHookResult = $this->hook_submit_beforeEmails();
		if (!$submitBeforeEmailsHookResult) { // All is ok (no spam maybe)
			
			$this->ok = 1; // sending allowed
			if ($this->cObj->cObjGetSingle($this->conf['allow.']['email2receiver'], $this->conf['allow.']['email2receiver.'])) { // main email is allowed
				$this->sendMail('recipient_mail'); // 2a. Email: Generate the Mail for the recipient (if allowed via TS)
			}
			if ($this->cObj->cObjGetSingle($this->conf['allow.']['email2sender'], $this->conf['allow.']['email2sender.']) // email to sender allowed in ts
				&& t3lib_div::validEmail($this->sessiondata[$this->cObj->data['tx_powermail_sender']]) // sender email is defined in backend and is an valid email address
				&& (!empty($this->subject_s) || !empty($this->cObj->data['tx_powermail_mailsender'])) // subject and body may not be empty to the same time
			) {
				$this->sendMail('sender_mail'); // 2b. Email: Generate the Mail for the sender (if allowed via TS and sender is selected and email exists)
			}
			if ($this->cObj->cObjGetSingle($this->conf['allow.']['dblog'], $this->conf['allow.']['dblog.'])) {
                $this->saveMail(); // 2c. Safe values to DB (if allowed via TS)
            }
			
		} else { // Spam hook is true (maybe spam recognized)
			$this->markerArray = array(); // clear markerArray
			$this->markerArray['###POWERMAIL_THX_ERROR###'] = $submitBeforeEmailsHookResult; // Fill ###POWERMAIL_THX_MESSAGE### with error message from Hook
		}
		
		// 2. Return Message to FE
		if ($this->ok == 1) $this->markerArray = $this->markers->GetMarkerArray($this->conf, $this->sessionfields, $this->cObj, 'thx'); // Fill markerArray
		$this->hook_submit_afterEmails(); // add hook for manipulation of data after E-Mails where sent
		$this->content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['thx'], $this->markerArray); // substitute Marker in Template
		$this->content = $this->dynamicMarkers->main($this->conf, $this->cObj, $this->content); // Fill dynamic locallang or typoscript markers
		$this->content = preg_replace('|###.*?###|i', '', $this->content); // Finally clear not filled markers
		$this->hook_submit_LastOne(); // add hook for manipulation thx message
		
		// 3. Additional db storing if wanted
		$this->dbImport->main($this->conf, $this->sessiondata, $this->cObj, $this->ok);
		
		// 4. Redirect if wanted
		$this->redirect();
		
		// 5. Now clear the session if option is set in TS
		$this->clearSession();
		
		// 6. Clear sessions of captcha
		$this->clearCaptchaSession();
		
		// 7. Check html templates
		if (!$this->div->subpartsExists($this->tmpl)) $this->content = $this->pi_getLL('error_templateNotFound', 'Template not found, check path to your powermail templates');
		
		return $this->content; // return HTML for THX Message
	}

	/**
	 * Returns a string wrapped inside quotes if it contains a comma character. Any '"'
	 * characters will be removed inside the string if it must be quoted.
	 *
	 * @param string $string the text
	 * @return string
	 */
	protected function quoteStringWithComma($string) {
		if (strpos($string, ',') !== FALSE) {
			$string = '"' . str_replace('"', '', $string) . '"';
		}

		return $string;
	}
	
	
	/** Function sendMail() generates mail for sender and receiver
     *
     * @param string $subpart subpart to set
     * @return void
     */
	function sendMail($subpart) {

        // Configuration
        $this->subpart = $subpart;
        $this->tmpl['emails'][$this->subpart] = $this->cObj->getSubpart($this->tmpl['emails']['all'], '###POWERMAIL_' . strtoupper($this->subpart) . '###'); // Content for HTML Template
        $this->markerArray = $this->markers->GetMarkerArray($this->conf, $this->sessionfields, $this->cObj, $this->subpart); // Fill markerArray
        $this->mailcontent[$this->subpart] = $this->cObj->substituteMarkerArrayCached(trim($this->tmpl['emails'][$this->subpart]), $this->markerArray); // substitute markerArray for HTML content
        $this->mailcontent[$this->subpart] = $this->dynamicMarkers->main($this->conf, $this->cObj, $this->mailcontent[$this->subpart]); // Fill dynamic locallang or typoscript markers
        $this->mailcontent[$this->subpart] = preg_replace('|###.*?###|i', '', $this->mailcontent[$this->subpart]); // Finally clear not filled markers
        $this->maildata = array();
        $this->attachments = array();

        // Set emails and names
        if ($this->subpart == 'recipient_mail') { // default settings: mail to receiver
            $this->maildata['receiver'] = $this->MainReceiver; // set receiver
            $this->maildata['sender'] = $this->sender; // set sender
            if (t3lib_div::validEmail($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['email'], $this->conf['email.'][$this->subpart . '.']['sender.']['email.']))) { // if overwrite value was set in ts
                $this->maildata['sender'] = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['email'], $this->conf['email.'][$this->subpart . '.']['sender.']['email.']); // overwrite sender email
            }
            $this->maildata['sendername'] = $this->username; // set sendername
            if (strlen($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['name'], $this->conf['email.'][$this->subpart . '.']['sender.']['name.'])) > 1) { // if sendername should be overwritten by typoscript
                $this->maildata['sendername'] = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['name'], $this->conf['email.'][$this->subpart . '.']['sender.']['name.']); // overwrite sender name
            }
            $this->maildata['subject'] = $this->subject_r; // set subject
            $this->maildata['cc'] = (isset($this->CCReceiver) ? $this->CCReceiver : ''); // carbon copy (take email addresses or nothing if not available)
        } elseif ($this->subpart == 'sender_mail') { // extended settings: mail to sender
            $this->maildata['receiver'] = $this->sender; // set receiver
            $this->maildata['sender'] = $this->MainReceiver; // set sender email address (take from ts or from first receiver)
            if (t3lib_div::validEmail($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['email'], $this->conf['email.'][$this->subpart . '.']['sender.']['email.']))) { // if overwrite value was set in ts
                $this->maildata['sender'] = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['email'], $this->conf['email.'][$this->subpart . '.']['sender.']['email.']); // overwrite sender email
            }
            $this->maildata['sendername'] = $this->sendername; // set sendername
            if (strlen($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['name'], $this->conf['email.'][$this->subpart . '.']['sender.']['name.'])) > 1) { // if sendername should be overwritten by typoscript
                $this->maildata['sendername'] = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['sender.']['name'], $this->conf['email.'][$this->subpart . '.']['sender.']['name.']); // overwrite sender name
            }
            $this->maildata['subject'] = $this->subject_s; // set subject
            $this->maildata['cc'] = ''; // no cc
        }

        $this->hook_submit_changeEmail(); // Last chance to manipulate the mail via hook
        $this->debug($this->subpart); // Debug output

        if ($this->email_send) {
            $subject = $this->dynamicMarkers->main($this->conf, $this->cObj, $this->div->marker2value($this->maildata['subject'], $this->sessiondata)); // mail subject (with dynamicmarkers and markers2value)
            $receiver = $this->maildata['receiver'];
            $from = $this->maildata['sender'];
            $fromName = ($this->maildata['sendername'] !== '') ? $this->quoteStringWithComma($this->maildata['sendername']) : $this->extKey;
            $returnPath = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['returnpath'], $this->conf['email.'][$this->subpart . '.']['returnpath.']);
            $returnPath = (t3lib_div::validEmail($returnPath)) ? $returnPath : $from; // return path
            $replyToEmail = $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['reply.']['email'], $this->conf['email.'][$this->subpart . '.']['reply.']['email.']); // set replyto email
            $replyToName = $this->quoteStringWithComma($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['reply.']['name'], $this->conf['email.'][$this->subpart . '.']['reply.']['name.'])); // set replyto name
			if (t3lib_div::int_from_ver(TYPO3_version) >= t3lib_div::int_from_ver('4.5')) {
				$this->useSwiftMailer = 1;
			}

            if ($this->useSwiftMailer){
                // new TYPO3 swiftmailer code
                $this->mail = t3lib_div::makeInstance('t3lib_mail_Message');
                $this->mail->setTo(array($receiver))
                    ->setFrom(array($from => $fromName))
                    ->setSubject($subject)
                    ->setReturnPath($returnPath)
                    ->setCharset($GLOBALS['TSFE']->metaCharset);

                if ($this->maildata['cc'] !== ''){
                    $this->mail->setBcc(t3lib_div::trimExplode(',', $this->maildata['cc']));
                }

                if ($replyToEmail !== '') {
                    if($replyToName !== '') {
                        $this->mail->setReplyTo(array($replyToEmail => $replyToName));
                    } else {
                        $this->mail->setReplyTo(array($replyToEmail));
                    }
                }
            } else {
                // old TYPO3 mail system code
				require_once(PATH_t3lib . 'class.t3lib_htmlmail.php');
                $this->mail = t3lib_div::makeInstance('t3lib_htmlmail'); // New object: TYPO3 mail class
                $this->mail->start(); // start htmlmail
                $this->mail->recipient = $receiver; // main receiver email address
                $this->mail->recipient_copy = $this->maildata['cc']; // cc field (other email addresses)
                $this->mail->subject = $subject;
                $this->mail->from_email = $from; // sender email address
                $this->mail->from_name = $fromName; // sender email name
                $this->mail->returnPath = $returnPath;
                $this->mail->replyto_email = $replyToEmail;
                $this->mail->replyto_name = $replyToName;
                $this->mail->charset = $GLOBALS['TSFE']->metaCharset; // set current charset
                $this->mail->defaultCharset = $GLOBALS['TSFE']->metaCharset; // set current charset
            }

            // add attachment (from user upload)
            if (isset($this->sessiondata['FILE']) && $this->conf['upload.']['attachment'] == 1) { // if there are uploaded files AND attachment to emails is activated via constants
                if (is_array($this->sessiondata['FILE']) && $this->subpart == 'recipient_mail') { // only if array and mail to receiver
                    $path = $this->div->correctPath($this->conf['upload.']['folder']);
                    foreach ($this->sessiondata['FILE'] as $fileName) { // one loop for every file
                        $file = $path . $fileName;
                        if (is_file(t3lib_div::getFileAbsFileName($file))) { // If file exists

                            // stdWrap for each file
                            $localCObj = t3lib_div::makeInstance('tslib_cObj'); // local cObj
                            $row = array ( // $row for using .field in typoscript
                             'file' => $file, // whole file with path
                             'filename' => $fileName, // filename
                             'path' => $path // path
                            );
                            $localCObj->start($row, 'tx_powermail_fields'); // enable .field in typoscript
                            $attachment = t3lib_div::getFileAbsFileName($localCObj->cObjGetSingle($this->conf['email.']['recipient_mail.']['attachment'], $this->conf['email.']['recipient_mail.']['attachment.']));

                            // add attachment
                            if ($this->useSwiftMailer){
                                $this->mail->attach(Swift_Attachment::fromPath($attachment));
                            } else {
                                $this->mail->addAttachment($attachment);
                            }
	                        $this->attachments[] .= $attachment;
                        }
                    }
                }
            }

            // add attachment (from typoscript)
            if ($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['addAttachment'], $this->conf['email.'][$this->subpart . '.']['addAttachment.'])) { // if there is an entry in the typoscript
                $files = t3lib_div::trimExplode(',', $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart . '.']['addAttachment'], $this->conf['email.'][$this->subpart . '.']['addAttachment.']), 1); // get an array with all files to add
                for ($i = 0; $i < count($files); $i ++) { // one loop for every file to add
                    if ($this->useSwiftMailer){
                        $this->mail->attach(Swift_Attachment::fromPath($files[$i]));
                    } else {
                        $this->mail->addAttachment(t3lib_div::getFileAbsFileName($files[$i])); // add attachment
                    }
                }
            }

            // add plain text part
            if ($this->conf['emailformat.'][$this->subpart] != 'html') { // add plaintext only if emailformat "both" or "plain"
                $plainText = $this->div->makePlain($this->mailcontent[$this->subpart]);

	            if ($this->conf['emailformat.']['enableTagsInPlainTextPart'] == 1) {
		            $plainText = htmlspecialchars_decode($plainText);
	            }

                if ($this->useSwiftMailer) {
                    $this->mail->addPart($plainText, 'text/plain');
                } else {
                    $this->mail->addPlain($plainText);
                }
            }

            // add html part
            if ($this->conf['emailformat.'][$this->subpart] != 'plain') { // add html only if emailformat "both" or "html"
                $html = $this->mailcontent[$this->subpart];
                if($this->useSwiftMailer) {
                    $this->mail->setBody($html, 'text/html');
                } else {
                    $this->mail->setHTML($this->mail->encodeMsg($html));
                }
            }

            $this->hook_submit_changeEmail2(); // hook with last chance to manipulate email values like attachment, etc...

            // send mail
	        if ($this->useSwiftMailer){
	            $this->mail->send();
	        } else {
		        $this->mail->send($receiver);
	        }
	        if ($this->conf['upload.']['delete'] == 1 && count($this->attachments) > 0) {
		        foreach ($this->attachments as $attachment) {
					t3lib_div::devlog('delete file ' . $attachment, 'powermail', 0);
					unlink($attachment); // delete attachment
		        }
	        }
        }
	}
	
	
	// Function saveMail() to save piVars and some more infos via xml to DB (tx_powermail_mails)
	function saveMail() {
		
		// Configuration
		$this->save_PID = $GLOBALS['TSFE']->id; // PID where to save: Take current page
		if (intval($this->conf['PID.']['dblog']) > 0) $this->save_PID = $this->conf['PID.']['dblog']; // PID where to save: Get it from TS if set
		if (intval($this->cObj->data['tx_powermail_pages']) > 0) $this->save_PID = $this->cObj->data['tx_powermail_pages']; // PID where to save: Get it from plugin
		
		// DB entry for table Tabelle: tx_powermail_mails
		$this->db_values = array (
			'pid' => intval($this->save_PID), // PID
			'tstamp' => time(), // save current time
			'crdate' => time(), // save current time
			'hidden' => $this->cObj->cObjGetSingle($this->conf['allow.']['hidden'], $this->conf['allow.']['hidden.']), // hidden = 0 or hidden = 1
			'formid' => ($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), // save pid
			'recipient' => $this->MainReceiver, // save receiver mail
			'cc_recipient' => (isset($this->CCReceiver) ? $this->CCReceiver : ''),
			'subject_r' => $this->dynamicMarkers->main($this->conf, $this->cObj, $this->div->marker2value($this->subject_r, $this->sessiondata)),
			'sender' => $this->sender, // save sender mail
			'content' => trim($this->mailcontent['recipient_mail']), // save content of receiver mail
			'piVars' => t3lib_div::array2xml($this->div->TSmanipulation($this->sessiondata, 'dblog', $this->conf, $this->cObj), '', 0, 'piVars'), // save values of session as xml
			'feuser' => ($GLOBALS['TSFE']->fe_user->user['uid'] > 0 ? $GLOBALS['TSFE']->fe_user->user['uid'] : '0'), // current feuser id
			'senderIP' => ($this->confArr['disableIPlog'] == 1 ? $this->pi_getLL('error_backend_noip') : t3lib_div::getIndpEnv('REMOTE_ADDR')), // save users IP address
			'UserAgent' => t3lib_div::getIndpEnv('HTTP_USER_AGENT'), // save user agent
			'Referer' => t3lib_div::getIndpEnv('HTTP_REFERER'), // save referer
			'SP_TZ' => $_SERVER['SP_TZ'] // save sp_tz if available
		);
		if ($this->dbInsert) $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_mails', $this->db_values); // DB entry
		$this->debug('db'); // Debug output
	}
	
	
	// Function emailSettings() defines some sender and receiver settings (name, email, etc...)
	function emailSettings() {
		// config
		$emails = ''; 
		$this->sendername = '';
		$this->sender = ($this->cObj->data['tx_powermail_sender'] && t3lib_div::validEmail($this->sessiondata[$this->cObj->data['tx_powermail_sender']]) ? $this->sessiondata[$this->cObj->data['tx_powermail_sender']] : $this->noReplyEmail); // email sender (if sender is selected and email exists)
		$this->username = $this->userName(); // name of sender (if field is selected)
		$this->subject_r = $this->cObj->data['tx_powermail_subject_r']; // Subject of mails (receiver)
		$this->subject_s = $this->cObj->data['tx_powermail_subject_s']; // Subject of mails (sender)
		
		// 1. Field receiver (and sendername!)
		if ($this->cObj->data['tx_powermail_recipient']) { // If receivers are listed in field receiver
			$emails = str_replace(array("\r\n", "\n\r", "\n", "\r", ";", "|"), ',', $this->cObj->data['tx_powermail_recipient']); // commaseparated list of emails
			$emails = $this->dynamicMarkers->main($this->conf, $this->cObj, $emails); // set dynamic markers receiver
			$emails = $this->div->marker2value($emails, $this->sessiondata); // make markers available in email receiver field
			$emailarray = t3lib_div::trimExplode(',', $emails, 1); // write every part to an array
			
			for ($i=0,$emails='';$i<count($emailarray);$i++) { // one loop for every key
				if (t3lib_div::validEmail($emailarray[$i])) $emails .= $emailarray[$i] . ', '; // if current value is an email write to $emails
				else $this->sendername .= $emailarray[$i] . ' '; // if current value is no email, take it for sender name and write to $this->sendername
			}
			if ($emails) $emails = substr(trim($emails), 0, -1); // delete last ,
			if (!empty($this->sendername)) $this->sendername = trim($this->sendername); // trim name
		}
		
		// 2. Field receiver from table
		elseif ($this->cObj->data['tx_powermail_recip_id'] && $this->cObj->data['tx_powermail_recip_table']) { // If emails from table was chosen
			$emails = $this->cObj->data['tx_powermail_recip_id']; // commaseparated list of emails
		}
		
		// 3. Field receiver query
		elseif ($this->conf['email.']['recipient_mail.']['email_query.']) { // If own select query is chosen
			$query = $this->secQuery($this->cObj->cObjGetSingle($this->conf['email.']['recipient_mail.']['email_query'], $this->conf['email.']['recipient_mail.']['email_query.'])); // secure function of query
			$query = $this->div->marker2value($query, $this->sessiondata, 1); // make markers available in email query
			
			$res = mysql_query($query); // mysql query
			
			if ($res && $query) { // If there is a result
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every result
					if (is_array($row)) { // if $row is an array
						foreach ($row as $key => $value) { // give me the key
							if (t3lib_div::validEmail($row[$key])) { // only if result is a valid email address
								$emails .= $row[$key] . ', '; // add email address with comma at the end
							}
						}
					}
				}
				if ($emails) $emails = substr(trim($emails), 0, -1); // delete last ,
			}
		}
		
		// 4. Split to main receiver and to all other receivers (aa@aa.com, bb@bb.com, cc@cc.com => 1. aa@aa.com / 2. bb@bb.com, cc@cc.com)
		if (isset($emails)) { // if email string is set
			if (strpos($emails,',') > 1) { // if there is a , in the string (more than only one email is set)
				$this->MainReceiver = substr($emails, 0, strpos($emails, ',')); // aa@aa.com
				$this->CCReceiver = substr($emails, trim(strpos($emails, ',') +1)); // bb@bb.com, cc@cc.com
			} else { // only one email is set
				$this->MainReceiver = $emails; // set mail
			}
		}
		
		// 5. If Sendername is not set, take default value
		if (empty($this->sendername)) { // if no sendername was defined (see 1.)
			$this->sendername = $this->extKey; // take "powermail" as sendername
		}
		
		return false;
	}
	
	
	// Function userName() defines sendername for email to receiver
	function userName() {
		if ($this->cObj->data['tx_powermail_sendername']) { // if name of sender was defined in flexform
			// config
			$fields = t3lib_div::trimExplode(',', $this->cObj->data['tx_powermail_sendername'], 1); // get array with list of all uids (0=>uid3, 1=>uid4)		
			$sendername = '';
			
			// let's go
			for ($i=0; $i<count($fields); $i++) { // one loop for every selected field
				if ($this->sessiondata[$fields[$i]]) $sendername .= $this->sessiondata[$fields[$i]].' '; // add value from current field
			}
			
			return trim($sendername); // return sendername
		
		} else return $this->extKey; // take "powermail" as sendername
	}

	
	/**
	 * Function redirect() forward the user to a new location after submit
	 *
	 * @return	void
	 */
	function redirect() {
		if ($this->ok) { // only if spamhook is not set
		
			// 1. Get Target from Flexform or Typoscript
			$redirectPidFromFlexform = trim($this->cObj->data['tx_powermail_redirect']);
			if (!empty($redirectPidFromFlexform)) {
				$target = $redirectPidFromFlexform; // get target from flexform in Backend
				
			} elseif (is_array($this->conf['redirect.']) && count($this->conf['redirect.']) > 0) {
				$target = $this->cObj->cObjGetSingle($this->conf['redirect'], $this->conf['redirect.']); // get target from TS
				
			} else {
				$target = 0; // disable target
			}
			
			
			// 2. Create Redirection Header
			if ($target) { // only if there is a redirect target
					
				$typolink_conf = array (
				  'returnLast' => 'url', // Give me only the string
				  'parameter' => $target, // target pid
				  'useCacheHash' => 0, // Don't use cache
				  'section' => '' // clear section value if any
				);
				$link = t3lib_div::locationHeaderUrl($this->cObj->typolink('x', $typolink_conf)); // Create target url

				// Set Header for redirect
				header('Location: ' . $link); 
				header('Connection: close');
		
			}
		}
	}
	
	
	// Function debug() enables debug output
	function debug($subpart) {
		if ($subpart != 'db') {
			// If debug output for email
			if ($this->conf['debug.']['output'] == 'all' || $this->conf['debug.']['output'] == 'email') { // only if debug output activated via constants
				$fileArray1 = $this->sessiondata['FILE']; // file array from session (user upload)
				$fileArray2 = t3lib_div::trimExplode(',', $this->cObj->cObjGetSingle($this->conf['email.'][$subpart.'.']['addAttachment'], $this->conf['email.'][$subpart.'.']['addAttachment.']), 1); // get an array with all files to add from typoscript
				$fileArray = array_merge((array) $fileArray1, (array) $fileArray2); // overall fileArray
				
				$debugarray = array (
					'receiver' => $this->maildata['receiver'] ? $this->maildata['receiver'] : 'SYSTEM NOTE: No receiver email address',
					'cc receiver' => $this->maildata['cc'] ? $this->maildata['cc'] : 'SYSTEM NOTE: No cc addresses',
					'sender' => $this->maildata['sender'] ? $this->maildata['sender'] : 'SYSTEM NOTE: No sender email address',
					'sendername' => $this->maildata['sendername'] ? $this->maildata['sendername'] : 'SYSTEM NOTE: No sender name',
					'reply email' => $this->cObj->cObjGetSingle($this->conf['email.'][$subpart.'.']['reply.']['email'], $this->conf['email.'][$subpart.'.']['reply.']['email.']),
					'reply name' => $this->cObj->cObjGetSingle($this->conf['email.'][$subpart.'.']['reply.']['name'], $this->conf['email.'][$subpart.'.']['reply.']['name.']),
					'return path' => (t3lib_div::validEmail($this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart.'.']['returnpath'], $this->conf['email.'][$this->subpart.'.']['returnpath.'])) ? $this->cObj->cObjGetSingle($this->conf['email.'][$this->subpart.'.']['returnpath'], $this->conf['email.'][$this->subpart.'.']['returnpath.']) : $this->maildata['sender']), // Return path
					'charset' => $GLOBALS['TSFE']->metaCharset ? $GLOBALS['TSFE']->metaCharset : 'SYSTEM NOTE: No charset set',
					'attachment' => (count($fileArray) > 0 ? $fileArray : 'SYSTEM NOTE: No attachments'),
					'subject' => $this->maildata['subject'] ? $this->maildata['subject'] : 'SYSTEM NOTE: No subject',
					'mailcontent (html)' => ($this->conf['emailformat.'][$this->subpart] != 'plain' ? $this->mailcontent[$subpart] : 'SYSTEM NOTE: Disabled via typoscript'),
					'mailcontent (plaintext)' => ($this->conf['emailformat.'][$this->subpart] != 'html' ? $this->div->makePlain($this->mailcontent[$subpart]) : 'SYSTEM NOTE: Disabled via typoscript')
				);
				$this->div->debug($debugarray, 'Email values ('.$subpart.')'); // Debug function (Array from Session)
			}
		} else {
			if ($this->conf['debug.']['output'] == 'all' || $this->conf['debug.']['output'] == 'db') { // only if debug output activated via constants
				$this->div->debug($this->db_values, 'DB values'); // Debug function (Array from Session)
			}
		}
	
	}
	

	// Function hook_submit_changeEmail() to add a hook and change the email datas (changing subject, receiver, sender, sendername)
	function hook_submit_changeEmail() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_SubmitEmailHook($this->subpart, $this->maildata, $this->sessiondata, $this->markerArray, $this); // Get new marker Array from other extensions
			}
		}
	}
	

	// Function hook_submit_changeEmail2() to add a hook and change the email datas short before sending (changing attachments, subject, receiver, sender, sendername)
	function hook_submit_changeEmail2() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook2'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook2'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
                $_procObj->PM_SubmitEmailHook2($this->subpart, $this->mail, $this); // Get new marker Array from other extensions
			}
		}
	}
	
	
	// Function hook_submit_beforeEmails() to add a hook at the end of this file to manipulate markers and content before emails where sent
	function hook_submit_beforeEmails() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitBeforeMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$this->PM_SubmitBeforeMarkerHook_return .= $_procObj->PM_SubmitBeforeMarkerHook($this, $this->markerArray, $this->sessiondata); // Get new marker Array from other extensions - if TRUE, don't send mails (maybe spam)
			}
			return $this->PM_SubmitBeforeMarkerHook_return; // Return value from hook if given
		} else { // if hook is not set
			return FALSE; // Return False is default (no spam, so emails could be sent)
		}
	}
	

	// Function hook_submit_LastOne() to add a hook and maybe change thx message
	function hook_submit_LastOne() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitLastOne'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitLastOne'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_SubmitLastOneHook($this->content, $this->conf, $this->sessiondata, $this->ok, $this); // Get new marker Array from other extensions
			}
		}
	}
	

	// Function hook_submit_afterEmails() to add a hook at the end of this file to manipulate markers and content after emails where sent
	function hook_submit_afterEmails() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitAfterMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitAfterMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_SubmitAfterMarkerHook($this,$this->markerArray,$this->sessiondata); // Get new marker Array from other extensions
			}
		}
	}
	
	
	// Function to clear the Session after submitting the form. Will only be cleared when option is selected in Constant-Editor oder set by TS
	function clearSession() {
		if ($this->ok) { // only if spamhook is not set
			if ($this->conf['clear.']['session'] == 1) { // If set in constants // setup
				$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), array()); // Generate Session without ERRORS
				$GLOBALS['TSFE']->storeSessionData(); // Save session*/
			}
		}
	}
	
	
	// Function clearCaptchaSession() clears already filled captcha sessions from captcha or sr_freecap
	function clearCaptchaSession() {
		if (t3lib_extMgm::isLoaded('sr_freecap', 0) || t3lib_extMgm::isLoaded('captcha', 0)) { // if captcha or freecap is loaded
			session_start(); // start session
			if (isset($_SESSION['tx_captcha_string'])) $_SESSION['tx_captcha_string'] = ''; // clear session of captcha
			if (isset($_SESSION['sr_freecap_attempts'])) $_SESSION['sr_freecap_attempts'] = 0; // clear session of sr_freecap
			if (isset($_SESSION['sr_freecap_word_hash'])) $_SESSION['sr_freecap_word_hash'] = false; // clear session of sr_freecap
		}
		if (t3lib_extMgm::isLoaded('wt_calculating_captcha', 0)) { // if wt_calculating_captcha is loaded
			unset($GLOBALS['TSFE']->fe_user->sesData['wt_calculating_captcha_value']); // delete value in session of wt_calculating_captcha
		}
		unset($GLOBALS['TSFE']->fe_user->sesData['powermail_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'])]['OK']); // clear all OK from Session (used e.g. from recaptcha)
	}
	
	
	// Function secQuery() disables query functions like UPDATE, TRUNCATE, DELETE, and so on
	function secQuery($string) {
		$error = 0; $failure = ''; // init 
		$notAllowed = array ( // list of all not allowed strings for querycheck
			'UPDATE',
			'TRUNCATE',
			'DELETE ', // deleted with space at the end (to allow "... where deleted = 0")
			'INSERT',
			'REPLACE',
			'HANDLER',
			'LOAD',
			'ALTER',
			'CREATE',
			'DROP',
			'RENAME',
			'DESCRIBE',
			'BEGIN',
			'COMMIT',
			'ROLLBACK',
			'LOCK',
			'REVOKE',
			'GRANT'
		);
		
		if (is_array($notAllowed)) { // only if array
			foreach ($notAllowed as $key => $value) { // one loop for every not allowed string
				if (strpos(strtolower($string), strtolower($value)) !== false) { // search for (e.g.) "delete" in string
					if (1) {
						$error = 1; // set error if found
						$failure .= '"'.$value.'", '; // Save error string
					}
				}
			}
		}
		if ($failure) $failure = substr(trim($failure), 0, -1); // delete last ,
		
		if ($error === 0) return $string; // return query if no error
		else { // if error
			echo 'Not allowed string ('.$failure.') in receiver sql query!'; // print error message
			return false; // no return
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_submit.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_submit.php']);
}

?>
