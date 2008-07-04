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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_markers.php'); // file for marker functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_dynamicmarkers.php'); // file for dynamicmarker functions

class tx_powermail_mandatory extends tslib_pibase {
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;
    var $scriptRelPath = 'pi1/class.tx_powermail_mandatory.php';    // Path to pi1 to get locallang.xml from pi1 folder

	function main($conf,$sessionfields) {
		$this->conf = $conf;
		$this->sessionfields = $sessionfields;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin
		
		// Instances
		$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_powermail_dynamicmarkers'); // New object: TYPO3 dynamicmarker function
		$this->markers = t3lib_div::makeInstance('tx_powermail_markers'); // New object: TYPO3 mail functions
		$this->markers->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions
		
		// Template
		$this->tmpl = array();
		$this->tmpl['mandatory']['all'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['mandatory']),'###POWERMAIL_MANDATORY_ALL###'); // Load HTML Template outer (work on subpart)
		$this->tmpl['mandatory']['item'] = $this->pibase->cObj->getSubpart($this->tmpl['mandatory']['all'],'###ITEM###'); // Load HTML Template inner (work on subpart)
		
		// Fill Markers
		$content_item = ''; $this->innerMarkerArray = array(); $fieldarray = array(); $this->error = 0;
		$this->markerArray = $this->markers->GetMarkerArray(); // Fill markerArray
		$this->markerArray['###POWERMAIL_TARGET###'] = $this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"useCacheHash"=>1)); // Fill Marker with action parameter
		$this->markerArray['###POWERMAIL_NAME###'] = $this->pibase->cObj->data['tx_powermail_title'].'_mandatory'; // Fill Marker with formname
		$this->markerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		
		// Different check functions
		$this->uniqueCheck(); // Check for unique fields and IP addresses
		$this->captchaCheck(); // Captcha Check
		$this->emailCheck(); // Email Check
		$this->regulareExpressions(); // Regulare Expression Check
		
		// Give me all fields of current content uid
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'tx_powermail_fields.uid, tx_powermail_fields.title, tx_powermail_fields.flexform',
			'tx_powermail_fields LEFT JOIN tx_powermail_fieldsets ON (tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid) LEFT JOIN tt_content ON (tx_powermail_fieldsets.tt_content = tt_content.uid)',
			$where_clause = 'tx_powermail_fieldsets.tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tt_content').tslib_cObj::enableFields('tx_powermail_fieldsets').tslib_cObj::enableFields('tx_powermail_fields'),
			$groupBy = '',
			$orderBy = 'tx_powermail_fieldsets.sorting ASC, tx_powermail_fields.sorting ASC',
			$limit
		);
		if ($res) { // If there is a result
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every field
				if ($this->pi_getFFvalue(t3lib_div::xml2array($row['flexform']),'mandatory') == 1) { // if in current xml mandatory == 1
					if (!is_array($this->sessionfields['uid'.$row['uid']])) { // first level
						if (!trim($this->sessionfields['uid'.$row['uid']]) || !isset($this->sessionfields['uid'.$row['uid']])) { // only if current value is not set in session (piVars)
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_mandatory_emptyfield').' <b>'.$row['title'].'</b>'; // set current error to sessionlist
						}
					} else { // second level (maybe for checkboxes
						if (isset($this->sessionfields['uid'.$row['uid']])) {
							$error=1; // errors on start (by default)
							foreach ($this->sessionfields['uid'.$row['uid']] as $key => $value) { // one loop for every field
								if ($this->sessionfields['uid'.$row['uid']][$key] != '') $error = 0; // set error
							}
							if ($error) $this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_mandatory_emptyfield').' <b>'.$row['title'].'</b>'; // set current error to sessionlist
						}
					}
				}
			}
		}
		
		// Check for errors
		if(isset($this->sessionfields['ERROR']) && is_array($this->sessionfields['ERROR'])) {
			foreach($this->sessionfields['ERROR'] as $key1 => $value1) { // one loop for every field with an error
				if(isset($this->sessionfields['ERROR'][$key1])) {
					foreach($this->sessionfields['ERROR'][$key1] as $key2 => $value2) { // one loop for every error on current field
						$this->error = 1; // mark as error
						$this->innerMarkerArray['###POWERMAIL_MANDATORY_LABEL###'] = $value2; // current field title (label)
						$content_item .= $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['mandatory']['item'], $this->innerMarkerArray); // add to content_item
					}
				}
			}
		}
		$subpartArray['###CONTENT###'] = $content_item;
		
		// Return
		$this->hook(); // adds hook
		$this->content = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['mandatory']['all'],$this->markerArray,$subpartArray); // substitute Marker in Template
		$this->content = $this->dynamicMarkers->main($this->conf, $this->pibase->cObj, $this->content); // Fill dynamic locallang or typoscript markers
		$this->content = preg_replace("|###.*?###|i","",$this->content); // Finally clear not filled markers
		if($this->error == 1) { // if there is an error
			$this->clearErrorsInSession();
			return $this->content; // return HTML
		}
	}
	
	
	// Function uniqueCheck() checks (if activated via constants) if a field is already filled with this value (like email addresses)
	function uniqueCheck() {
		// config
		$uniquearray = t3lib_div::trimExplode(',', $this->conf['enable.']['unique'],1); // Get unique constants from ts
		$confarray = unserialize($GLOBALS['TSFE']->TYPO3_CONF_VARS['EXT']['extConf'][$this->extKey]); // get config from localconf.php
		
		// let's go
		if (is_array($uniquearray) && isset($uniquearray)) {
			foreach ($uniquearray as $value) {
				
				// check for unique uids
				if (is_numeric(str_replace('uid','',strtolower($value)))) { // like uid11
					
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // Get all emails with any entry of current value
						'piVars',
						'tx_powermail_mails',
						$where_clause = 'pid = '.($this->conf['PID.']['dblog'] ? intval($this->conf['PID.']['dblog']) : $GLOBALS['TSFE']->id).' AND piVars LIKE "%'.$this->sessionfields[strtolower($value)].'%"'.tslib_cObj::enableFields('tx_powermail_mails'),
						$groupBy = '',
						$orderBy = '',
						$limit = ''
					);
					if ($res) { // If there is a result
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every found email
							if ($row['piVars']) { // entry found
								$vars = t3lib_div::xml2array($row['piVars'], 'piVars'); // array of values
								if (!is_array($vars)) $vars = utf8_encode(t3lib_div::xml2array($row['piVars'], 'piVars'));
								
								if ($vars[strtolower($value)] == $this->sessionfields[strtolower($value)]) { // entry found
									$this->sessionfields['ERROR'][strtolower($value)][] = sprintf($this->pi_getLL('error_unique_field', '%s was already used'), $this->sessionfields[strtolower($value)]); // add errormsg
									break; // stop loop
								}
							}
						}
					}
					
				}
				
				// check for IP address
				elseif (strtolower($value) == 'ip' && $confarray['disableIPlog'] != 1) { // value == ip AND IP log is not disabled
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // get any entry with same IP address like current user
						'senderIP',
						'tx_powermail_mails',
						$where_clause = 'pid = '.($this->conf['PID.']['dblog'] ? intval($this->conf['PID.']['dblog']) : $GLOBALS['TSFE']->id).' AND senderIP = "'.$_SERVER['REMOTE_ADDR'].'"'.tslib_cObj::enableFields('tx_powermail_mails'),
						$groupBy = '',
						$orderBy = '',
						$limit = 1
					);
					if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if ($row['senderIP']) { // IP address found
						$this->sessionfields['ERROR'][strtolower($value)][] = sprintf($this->pi_getLL('error_unique_ip', 'IP address %s already made an entry'), $_SERVER['REMOTE_ADDR']); // add errormsg
					}
				}
			}
		}
	}
	
	
	// Functions regulareExpressions() checks values
	function regulareExpressions() {
		// Config - set regulare expressions for autocheck
		$autoarray = array (
			'email' => "^[_a-z0-9]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.)+([a-z0-9]{2,4})$^",
			'url' => "^(http://)?([a-z0-9-]+\.)+([a-z0-9-]{2,3})$^",
			'numbers' => "/[0-9]+$/",
			'phone' => "/[0-9\/+-]+$/",
			'alphanum' => "/[a-zA-Z0-9]/"
		);
		
		// Let's go and check
		if (isset($this->conf['validate.']) && is_array($this->conf['validate.'])) { // Only if any validation is set per typoscript
			foreach ($this->conf['validate.'] as $key => $value) { // One loop for every validation
				// autocheck
				if ($this->conf['validate.'][$key]['auto']) { // If autocheck of current value is active
					if (isset($autoarray[$this->conf['validate.'][$key]['auto']])) { // if regulare expression in $autoarray
						if ($this->sessionfields[str_replace('.','',$key)]) { // if there is a value in the field, which to check
							
							// Check
							if (!preg_match($autoarray[$this->conf['validate.'][$key]['auto']], $this->sessionfields[str_replace('.','',$key)])) { // If check failed
								$this->sessionfields['ERROR'][str_replace('.','',$key)][] = ($this->conf['validate.'][$key]['errormsg']?$this->conf['validate.'][$key]['errormsg']:$this->pi_getLL('error_expression_validation')); // write errormessage
							}
							
						}
					}
				} elseif ($this->conf['validate.'][$key]['expression']) { // regulare expression
					if ($this->sessionfields[str_replace('.','',$key)]) { // if there is a value in the field, which to check
						
						// Check
						if (!preg_match($this->div_functions->marker2value($this->conf['validate.'][$key]['expression'],$this->sessionfields), $this->sessionfields[str_replace('.','',$key)])) { // If check failed
							$this->sessionfields['ERROR'][str_replace('.','',$key)][] = ($this->conf['validate.'][$key]['errormsg']?$this->conf['validate.'][$key]['errormsg']:$this->pi_getLL('error_expression_validation')); // write errormessage
						}
						
					}
				}
			}
		}
	}
	
	
	// Function emailCheck() checks if sender email address is a real email address, if not write error to session
	function emailCheck() {
		if($this->pibase->cObj->data['tx_powermail_sender'] && is_array($this->sessionfields)) { // If email address from sender is set in backend
			if($this->sessionfields[$this->pibase->cObj->data['tx_powermail_sender']]) { // if there is content in the email sender field
				if(!t3lib_div::validEmail($this->sessionfields[$this->pibase->cObj->data['tx_powermail_sender']])) { // Value is not an email address
					$this->sessionfields['ERROR'][str_replace('uid','',$this->pibase->cObj->data['tx_powermail_sender'])][] = $this->pi_getLL('error_validemail'); // write error message to session
				} else { // Syntax of email address is correct - check for MX Record (if activated via constants)
					if( !$this->div_functions->checkMX( $this->sessionfields[$this->pibase->cObj->data['tx_powermail_sender']] ) && $this->conf['email.']['checkMX'] ) $this->sessionfields['ERROR'][str_replace('uid','',$this->pibase->cObj->data['tx_powermail_sender'])][] = $this->pi_getLL('error_nomx'); // write error message to session
				}
			}
		}
	}
	
	
	// Function captchaCheck check if captcha fields are within current content and set errof if value is wrong
	function captchaCheck() {
		if(t3lib_extMgm::isLoaded('captcha',0) || t3lib_extMgm::isLoaded('sr_freecap',0)) { // only if a captcha extension is loaded
		
			// Give me all captcha fields of current tt_content
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'tx_powermail_fields.uid',
				'tx_powermail_fields LEFT JOIN tx_powermail_fieldsets ON (tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid) LEFT JOIN tt_content ON (tx_powermail_fieldsets.tt_content = tt_content.uid)',
				$where_clause = 'tx_powermail_fields.formtype = "captcha" AND tx_powermail_fieldsets.tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tt_content').tslib_cObj::enableFields('tx_powermail_fieldsets').tslib_cObj::enableFields('tx_powermail_fields'),
				$groupBy = '',
				$orderBy = 'tx_powermail_fieldsets.sorting ASC, tx_powermail_fields.sorting ASC',
				$limit = 1
			);
			if ($res) { // If there is a result
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every captcha field
					
					if (t3lib_extMgm::isLoaded('sr_freecap',0) && $this->conf['captcha.']['use'] == 'sr_freecap') { // use sr_freecap if available
						
						session_start(); // start session
						if($this->sessionfields['uid'.$row['uid']] == '') { // if captcha value is empty
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_empty'); // write error message to session
						}
						
						elseif (
							($_SESSION['sr_freecap_word_hash'] != md5($this->sessionfields['uid'.$row['uid']])) && 
							($_SESSION['sr_freecap_word_hash'] != md5($this->sessionfields['uid'.$row['uid']]."\n"))) {
							
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_wrong'); // write error message to session
							
						}
					}
					
					elseif (t3lib_extMgm::isLoaded('captcha',0) && $this->conf['captcha.']['use'] == 'captcha') { // use captcha if available
					
						session_start(); // start session
						$captchaStr = $_SESSION['tx_captcha_string']; // get captcha value from session
						
						if ($this->sessionfields['uid'.$row['uid']] == '') { // if captcha value is empty
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_empty'); // write error message to session
						}
						
						elseif ($this->sessionfields['uid'.$row['uid']] != $captchaStr) { // if captcha value is wrong
							$this->sessionfields['ERROR'][$row['uid']][] = $this->pi_getLL('error_captcha_wrong'); // write error message to session
						}
						
					}
				}
			}
			
		}
	}
	
	
	// Function clearErrorsInSession() removes all global errors, which are marked as an error in the session
	function clearErrorsInSession() {
		// Set Session (overwrite all values)
		unset($this->sessionfields['ERROR']); // remove all error messages
		$GLOBALS['TSFE']->fe_user->setKey("ses", $this->extKey.'_'.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']), $this->sessionfields); // Generate Session without ERRORS
		$GLOBALS['TSFE']->storeSessionData(); // Save session
	}
	
	
	// Function hook() to enable manipulation datas with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_ConfirmationHook($this->error,$this->markerArray,$this->innerMarkerArray,$this->sessionfields,$this); // Open function to manipulate data
			}
		}
	}


	//function for initialisation.
	// to call cObj, make $this->pibase->cObj->function()
	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_mandatory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_mandatory.php']);
}

?>