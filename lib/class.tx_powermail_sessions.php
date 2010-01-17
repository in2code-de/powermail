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
 * @author	Alexander Kellner, Mischa Heiﬂmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
 * @package	TYPO3
 * @subpackage	tx_powermail
 */
class tx_powermail_sessions extends tslib_pibase {

	var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';    // Path to pi1 to get locallang.xml from pi1 folder
	var $extendedSessionValues = array('FILE', 'ERROR', 'OK'); // define other keys which could be listed in a session
	
	
	// Function setSession() to save all piVars to a session
	function setSession($conf, $piVars, $cObj, $overwrite = 1) {
		// conf
		$this->conf = $conf;
		$this->cObj = $cObj;
		
		// start
		if (isset($piVars)) { // Only if piVars are existing
			// get old values before overwriting
			if ($overwrite == 0) { // get old values so, it can be set again
				$oldPiVars = $this->getSession($this->conf, $this->cObj, 0); // Get Old piVars from Session (without not allowed piVars)
				if (isset($oldPiVars) && is_array($oldPiVars)) $piVars = array_merge($oldPiVars, $piVars); // Add old piVars to new piVars
			}
			// Set Session (overwrite all values)
			$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), $piVars); // Generate Session with piVars array
			$GLOBALS['TSFE']->storeSessionData(); // Save session
		}
	}
	
	
	// Function getSession() to get all saved session data in an array
	function getSession($conf, $cObj, $all = 1) {
		// conf
		$this->conf = $conf;
		$this->cObj = $cObj;
		
		// start
		$piVars = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'])); // Get piVars from Session
		
		if ($all == 0) { // delete not allowed values from piVars
			if (isset($piVars) && is_array($piVars)) {
				foreach($piVars as $key => $value) { // one loop for every piVar
					if (!is_numeric(str_replace('uid','',$key)) && !in_array($key, $this->extendedSessionValues)) { // all values which are not like uid3 && Not especially values
						unset($piVars[$key]); // delete current value (like mailID or sendnow)
					}
				}
			}
		}
		
		if (isset($piVars)) return $piVars;
	}
	
	
	// Function deleteSession() 
	function deleteSession($conf, $cObj, $uid) {
		$this->conf = $conf;
		$this->cObj = $cObj;
		
		if (!is_array($uid)) { // is not an array
			if ($uid == -1) { // delete all
				
				$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), array()); // Overwrite Session with empty array
				$GLOBALS['TSFE']->storeSessionData(); // Save session
				
			} elseif ($uid > 0) { // delete only one value from session
				
				$oldPiVars = $this->getSession($this->conf, $this->cObj); // Get all old piVars from Session
				$oldvalue = $oldPiVars['uid'.$uid]; // filename
				if (count($oldPiVars['FILE']) > 0) { // if there are values in the FILE array
					foreach ($oldPiVars['FILE'] as $key => $value) { // one loop for every file in array
						if ($value == $oldvalue) unset($oldPiVars['FILE'][$key]); // delete FILE array value
					}
				}
				unset($oldPiVars['uid'.$uid]); // Delete one uid
				$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), $oldPiVars); // Overwrite Session with array
				$GLOBALS['TSFE']->storeSessionData(); // Save session
				
			}
		} else { // is an array (multiple upload)
			if (count($uid) > 0) {
				$oldPiVars = $this->getSession($this->conf, $this->cObj); // Get all old piVars from Session
				foreach ($uid as $key => $value) {
					unset($oldPiVars['FILE'][$key]); // delete FILE array value
				}
				$GLOBALS['TSFE']->fe_user->setKey('ses', $this->extKey.'_'.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), $oldPiVars); // Overwrite Session with array
				$GLOBALS['TSFE']->storeSessionData(); // Save session
			}
		}
	}
	
	
	/**
	 * change Date to manipulate piVars (maybe uploads should be changed, sender email address should be valid, etc..)
	 *
	 * @param	array		$piVars: array with GET and POST params
	 * @return	array		$piVars: array with manipulated GET and POST params
	 */
	function changeData($piVars) {
		
		// config
		$this->pi_loadLL();
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // Create new instance for div class
		
		// check for uploaded files and copy them...
		$this->allowedFileExtensions = t3lib_div::trimExplode(',', $this->conf['upload.']['file_extensions'], 1); // get all allowed fileextensions
		$this->uids = '';
		if (is_array($_FILES['tx_powermail_pi1']['name'])) {
			foreach ($_FILES['tx_powermail_pi1']['name'] as $key => $value) { // one loop for every piVar
				if(is_numeric(str_replace('uid', '', $key))) $this->uids .= str_replace('uid', '', $key) . ','; // generate uid list like 5,6,77,23,
			}
			if (strlen($this->uids) > 0) $this->uids = substr($this->uids, 0, -1); // delete last ,
		}
		if (trim($this->uids) != '') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // search for all uploads fields within piVars
				'uid',
				'tx_powermail_fields',
				$where_clause = 'uid IN (' . $this->uids . ') AND (formtype = "file" OR formtype="multiupload")'  .tslib_cObj::enableFields('tx_powermail_fields'),
				$groupBy = '',
				$orderBy = '',
				$limit =''
			);
			if ($res) { // If there is a result
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every uploadfield 
					if ($_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']]) { // if there is a content in current upload field
						if (is_array($_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']])) { // is this an array? Can be for multiple file-upload
							foreach ($_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] as $key => $file) {
								if ($file != '') {
									$fileinfo = pathinfo($file); // get info about uploaded file
									
									// filename like name_md5ofnameandtime.ext
									$newfilename = str_replace('.' . $fileinfo['extension'], '', $file); // orig filename without extension
									$newfilename = str_replace(' ', '_', $newfilename); // remove space
									$newfilename .= '_'; // glue
									$newfilename .= t3lib_div::md5int($file . time()); // hash
									$newfilename .= '.' . $fileinfo['extension']; // new extension

									if (filesize($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']][$key]) < ($this->conf['upload.']['filesize'] * 1024)) { // filesize check
										if (in_array(strtolower($fileinfo['extension']), $this->allowedFileExtensions)) { // if current fileextension is allowed
											if (($this->conf['upload.']['mimecheck'] && $this->div->mimecheck($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']][$key], $newfilename)) || $this->conf['upload.']['mimecheck'] != 1) { // mimecheck off OR mimecheck true
												
												// upload copy move uploaded files to destination
												if (t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']][$key], t3lib_div::getFileAbsFileName($this->div->correctPath($this->conf['upload.']['folder']) . $newfilename))) {
													$piVars['uid' . $row['uid']] = $newfilename; // write new filename to session (area for normal fields)
													$piVars['FILE'][] = $newfilename; // write new filename to session (area for files)
												} else { // could not be copied (maybe write permission error or wrong path)
													$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_main') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']][$key] . '</b>'; // write error to session
												}
												
											} else { // mimecheck don't fit
												$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_mimetype') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']][$key] . '</b>'; // write error to session
											}
										} else { // fileextension is not allowed
											$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_extension') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']][$key] . '</b>'; // write error to session
										}
									} else { // filesize to large
										$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_toolarge') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']][$key] . '</b>'; // write error to session
									}
								}
							}
						} else { // if no array given use files instead
							$fileinfo = pathinfo($_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']]); // get info about uploaded file
							
							// filename like name_md5ofnameandtime.ext
							$newfilename = str_replace('.' . $fileinfo['extension'], '', $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']]); // orig filename without extension
							$newfilename = str_replace(' ', '_', $newfilename); // remove space
							$newfilename .= '_'; // glue
							$newfilename .= t3lib_div::md5int($_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] . time()); // hash
							$newfilename .= '.' . $fileinfo['extension']; // new extension

							if (filesize($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']]) < ($this->conf['upload.']['filesize'] * 1024)) { // filesize check
								if (in_array(strtolower($fileinfo['extension']), $this->allowedFileExtensions)) { // if current fileextension is allowed
									if (($this->conf['upload.']['mimecheck'] && $this->div->mimecheck($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']], $newfilename)) || $this->conf['upload.']['mimecheck'] != 1) { // mimecheck off OR mimecheck true
										
										// upload copy move uploaded files to destination
										if (t3lib_div::upload_copy_move($_FILES['tx_powermail_pi1']['tmp_name']['uid' . $row['uid']], t3lib_div::getFileAbsFileName($this->div->correctPath($this->conf['upload.']['folder']) . $newfilename))) {
											$piVars['uid' . $row['uid']] = $newfilename; // write new filename to session (area for normal fields)
											$piVars['FILE'][] = $newfilename; // write new filename to session (area for files)
										} else { // could not be copied (maybe write permission error or wrong path)
											$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_main') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] . '</b>'; // write error to session
										}
										
									} else { // mimecheck don't fit
										$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_mimetype') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] . '</b>'; // write error to session
									}
								} else { // fileextension is not allowed
									$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_extension') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] . '</b>'; // write error to session
								}
							} else { // filesize to large
								$piVars['ERROR'][$row['uid']][] = $this->pi_getLL('locallangmarker_error_file_toolarge') . ' <b>' . $_FILES['tx_powermail_pi1']['name']['uid' . $row['uid']] . '</b>'; // write error to session
							}
						}
					}
					
				}
			}
		}
		
		return $piVars;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_sessions.php']);
}

?>