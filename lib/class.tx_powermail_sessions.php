<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Alexander Kellner, Mischa Heiﬂmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
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

/**
 * Class with collection of different functions (like string and array functions)
 *
 * @author	Mischa Heiﬂmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@einpraegsam.net>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_sessions extends tslib_pibase {

	var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';    // Path to pi1 to get locallang.xml from pi1 folder
	
	
	// Function setSession() to save all piVars to a session
	function setSession($piVars,$overwrite = 1) {
		if(isset($piVars)) { // Only if piVars are existing
			// get old values before overwriting
			if($overwrite == 0) { // get old values so, it can be set again
				$oldPiVars = $this->getSession(0); // Get Old piVars from Session (without not allowed piVars)
				if(isset($oldPiVars) && is_array($oldPiVars)) $piVars = array_merge($oldPiVars, $piVars); // Add old piVars to new piVars
			}
			// Set Session (overwrite all values)
			$GLOBALS['TSFE']->fe_user->setKey("ses", $this->extKey.'_'.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']), $piVars); // Generate Session with piVars array
			$GLOBALS['TSFE']->storeSessionData(); // Save session
		}
	}
	
	
	// Function getSession() to get all saved session data in an array
	function getSession($all = 1) {
		$piVars = $GLOBALS['TSFE']->fe_user->getKey("ses", $this->extKey.'_'.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid'])); // Get piVars from Session
		//$piVars = array_map('html_entity_decode',$piVars);
		
		if($all == 0) { // delete not allowed values from piVars
			if(isset($piVars) && is_array($piVars)) {
				foreach($piVars as $key => $value) { // one loop for every piVar
					if(!is_numeric(str_replace('uid','',$key)) && $key != 'FILE' && $key != 'ERROR') {
						unset($piVars[$key]); // delete current value (like mailID or sendnow)
					}
				}
			}
		}
		
		if(isset($piVars)) return $piVars;
	}
	
	
	// change Date to manipulate piVars (maybe uploads should be changed, sender email address should be valid, etc..)
	function changeData($piVars) {
		
		// config
		$this->pi_loadLL();
		
		
		// 1. CHECK FOR UPLOAD FIELDS AND COPY UPLOADED FILE...
		$this->allowedFileExtensions = t3lib_div::trimExplode(',',$this->conf['upload.']['file_extensions'],1); // get all allowed fileextensions
		$this->uids = '';
		if(is_array($_FILES['tx_powermail_pi1']['name'])) {
			foreach ($_FILES['tx_powermail_pi1']['name'] as $key => $value) { // one loop for every piVar
				if(is_numeric(str_replace('uid','',$key))) $this->uids .= str_replace('uid','',$key).','; // generate uid list like 5,6,77,23,
			}
			if(strlen($this->uids) > 0) $this->uids = substr($this->uids,0,-1); // delete last ,
		}
		if(trim($this->uids) != '') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // search for all uploads fields within piVars
				'uid',
				'tx_powermail_fields',
				$where_clause = 'uid IN ('.$this->uids.') AND (formtype = "file" OR formtype="multiupload")'.tslib_cObj::enableFields('tx_powermail_fields'),
				$groupBy = '',
				$orderBy = '',
				$limit =''
			);
			if ($res) { // If there is a result
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every uploadfield 
					if($_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']]) { // if there is a content in current upload field
						if(is_array($_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']])) { // is this an array? Can be for multiple file-upload
							foreach($_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']] as $key => $file) {
								if($file != ''){
									$fileinfo = pathinfo($file); // get info about uploaded file
									$newfilename = str_replace('.'.$fileinfo['extension'],'',$file).'_'.t3lib_div::md5int($file.time()).'.'.$fileinfo['extension']; // filename like name_md5ofnameandtime.ext

									if(filesize($_FILES['tx_powermail_pi1']['tmp_name']['uid'.$row['uid']][$key]) < ($this->conf['upload.']['filesize'] * 1024)) { // filesize check
										if(in_array(strtolower($fileinfo['extension']), $this->allowedFileExtensions)) { // if current fileextension is allowed
											// upload copy move uploaded files to destination
											if(t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['uid'.$row['uid']][$key], t3lib_div::getFileAbsFileName($this->div_functions->correctPath($this->conf['upload.']['folder']).$newfilename))) {
												$piVars['uid'.$row['uid']] = $newfilename; // write new filename to session (area for normal fields)
												$piVars['FILE'][] = $newfilename; // write new filename to session (area for files)
											} else { // could not be copied (maybe write permission error or wrong path)
												$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_main').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']][$key].'</b>'; // write error to session
											}
										} else { // fileextension is not allowed
											$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_extension').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']][$key].'</b>'; // write error to session
										}
									} else { // filesize to large
										$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_toolarge').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']][$key].'</b>'; // write error to session
									}
								}
							}
						}
						else { // if no array given use files instead
							$fileinfo = pathinfo($_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']]); // get info about uploaded file
							$newfilename = str_replace('.'.$fileinfo['extension'],'',$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']]).'_'.t3lib_div::md5int($_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']].time()).'.'.$fileinfo['extension']; // filename like name_md5ofnameandtime.ext

							if(filesize($_FILES['tx_powermail_pi1']['tmp_name']['uid'.$row['uid']]) < ($this->conf['upload.']['filesize'] * 1024)) { // filesize check
								if(in_array(strtolower($fileinfo['extension']), $this->allowedFileExtensions)) { // if current fileextension is allowed
									// upload copy move uploaded files to destination
									if(t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['uid'.$row['uid']], t3lib_div::getFileAbsFileName($this->div_functions->correctPath($this->conf['upload.']['folder']).$newfilename))) {
										$piVars['uid'.$row['uid']] = $newfilename; // write new filename to session (area for normal fields)
										$piVars['FILE'][] = $newfilename; // write new filename to session (area for files)
									} else { // could not be copied (maybe write permission error or wrong path)
										$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_main').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']].'</b>'; // write error to session
									}
								} else { // fileextension is not allowed
									$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_extension').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']].'</b>'; // write error to session
								}
							} else { // filesize to large
								$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_toolarge').' <b>'.$_FILES['tx_powermail_pi1']['name']['uid'.$row['uid']].'</b>'; // write error to session
							}
						}
					}
					
				}
			}
		}
		
		
		// 2. CHECK IF EMAIL FIELD HAS AN VALID EMAIL
		if($this->pibase->cObj->data['tx_powermail_sender']) { // if in backend is an sender field defined
			if($piVars[$this->pibase->cObj->data['tx_powermail_sender']]) { // if field is not empty
				if(!t3lib_div::validEmail($piVars[$this->pibase->cObj->data['tx_powermail_sender']])) { // check if string is a valid email
					$piVars['ERROR'][str_replace('uid','',$this->pibase->cObj->data['tx_powermail_sender'])][] = $this->pi_getLL('locallangmarker_error_validemail').' <b>'.$piVars[$this->pibase->cObj->data['tx_powermail_sender']].'</b>'; // set error to piVars
				}
			}
		}
		
		return $piVars;
	}


	// Function for initialisation.
	// to call cObj, make $this->pibase->cObj->function()
	function init($conf,$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
		$this->div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->div_functions->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php']);
}

?>