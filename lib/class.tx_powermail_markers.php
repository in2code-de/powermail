<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Alex Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.YYYY@heissmann.org>
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
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_geoip.php'); // file for geo info

class tx_powermail_markers extends tslib_pibase {

    var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';    // Path to pi1 to get locallang.xml from pi1 folder
    var $locallangmarker_prefix = 'locallangmarker_'; // prefix for automatic locallangmarker
    
    // Function GetMarkerArray() to set global Markers for Emails and THX message
    function GetMarkerArray($conf, $sessionfields, $cObj, $what = '') {
        
        // Configuration
		$this->conf = $conf;
		$this->cObj = $cObj;
		$this->what = $what;
		$this->geo = t3lib_div::makeInstance('tx_powermail_geoip'); // Instance with geo class
       	$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->geoArray = $this->geo->main($this->conf); // Get geoinfo array
        $this->markerArray['###POWERMAIL_ALL###'] = $content_item = ''; $this->markerArray = array(); // init
        $this->sessiondata = $this->getSession($what); // fill variable with values from session	
        $this->notInMarkerAll = t3lib_div::trimExplode(',', $this->conf['markerALL.']['notIn'], 1); // choose which fields should not be listed in marker ###ALL### (ERROR is never allowed to be shown)
        $this->tmpl['all']['all'] = $this->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['all']), "###POWERMAIL_ALL###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->tmpl['all']['item'] = $this->cObj->getSubpart($this->tmpl['all']['all'], "###ITEM###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->hook_markerArray(); // adds hook
		 

		if (isset($this->sessiondata) && is_array($this->sessiondata)) {
			// normal markers
            foreach ($this->sessiondata as $k => $v) { // One loop for every piVar
				if ($k == 'FILE' && count($v) > 1) { // only if min two files uploaded (don't show uploaded files two times if only one upload field)
				    $i = 1;
					foreach ($v as $key => $file) {
						$this->markerArray['###' . strtoupper($k) . '_' . $key . '###'] = stripslashes($this->div->nl2br2($file)); // Filename
						$this->markerArray['###LABEL_' . strtolower($k) . '_' . $key . '###'] = sprintf($this->pi_getLL('locallangmarker_confirmation_files','Attached file %s: '),$i); // Label to filename
						if (!in_array(strtoupper($k), $this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###', $this->notInMarkerAll)) {
							$markerArray['###POWERMAIL_LABEL###'] = sprintf($this->pi_getLL('locallangmarker_confirmation_files','Attached file %s: '),$i);
							$markerArray['###POWERMAIL_VALUE###'] = stripslashes($this->div->nl2br2($file));
						}
						$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v); // add hook
						$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray);
					$i++;
					}
				}
				else {
					if (is_numeric(str_replace('uid', '', $k))) { // use only piVars like UID555
						if (!is_array($v)) { // standard: value is not an array
							if (is_numeric(str_replace('uid', '', $k))) { // check if key is like uid55
								$this->markerArray['###' . strtoupper($k) . '###'] = stripslashes($this->div->nl2br2($v)); // fill ###UID55###
								$this->markerArray['###' . strtolower($k) . '###'] = stripslashes($this->div->nl2br2($v)); // fill ###uid55###
								$this->markerArray['###LABEL_' . strtoupper($k) . '###'] = $this->GetLabelfromBackend($k,$v); // fill ###LABEL_UID55###
								$this->markerArray['###LABEL_' . strtolower($k) . '###'] = $this->GetLabelfromBackend($k,$v); // fill ###label_uid55###

								// ###POWERMAIL_ALL###
								if (!in_array(strtoupper($k),$this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###',$this->notInMarkerAll)) {
									$markerArray['###POWERMAIL_LABEL###'] = $this->GetLabelfromBackend($k,$v);
									$markerArray['###POWERMAIL_VALUE###'] = stripslashes($this->div->nl2br2($v));
									if ($this->conf['markerALL.']['hideLabel'] == 1 && $markerArray['###POWERMAIL_VALUE###'] || $this->conf['markerALL.']['hideLabel'] == 0) { // if hideLabel on in backend: add only if value exists
										$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray); // add line
									}
									$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v); // add hook
								}
							}
						} else { // value is still an array (needed for e.g. checkboxes tx_powermail_pi1[uid55][0])
							$i=0; // init counter
							foreach ($v as $kv => $vv) { // One loop for every piVar
								if (is_numeric(str_replace('uid','',$k))) { // check if key is like uid55
									if ($vv) { // if value exists
										$this->markerArray['###' . strtoupper($k) . '_' . $kv . '###'] = stripslashes($this->div->nl2br2($vv)); // fill ###UID55_0###
										$this->markerArray['###' . strtolower($k) . '_' . $kv . '###'] = stripslashes($this->div->nl2br2($vv)); // fill ###uid55_0###
										//$this->markerArray['###'.strtoupper($k).'###'] .= ($i != 0 ? ', ' : '') . stripslashes($this->div->nl2br2($vv)); // fill ###UID55### (comma between every value)
										$this->markerArray['###' . strtoupper($k) . '###'] .= ($i != 0 ? $this->cObj->stdWrap($this->conf['field.']['checkboxSplitSign'], $this->conf['field.']['checkboxSplitSign.']) : '') . stripslashes($this->div->nl2br2($vv)); // fill ###UID55### (comma between every value)
										$this->markerArray['###LABEL_' . strtoupper($k) . '###'] = $this->GetLabelfromBackend($k,$v); // fill ###LABEL_UID55###
										$this->markerArray['###LABEL_' . strtolower($k) . '###'] = $this->GetLabelfromBackend($k,$v); // fill ###label_uid55###
	
										// ###POWERMAIL_ALL###
										if (!in_array(strtoupper($k),$this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###',$this->notInMarkerAll)) {
											$markerArray['###POWERMAIL_LABEL###'] = $this->GetLabelfromBackend($k,$v);
											$markerArray['###POWERMAIL_VALUE###'] = stripslashes($this->div->nl2br2($vv));
											$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v, $kv, $vv); // add hook
											$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'],$markerArray);
										}
										$i++; // increase counter
									}
								}
							}
						}
					}
				}
            }
			// geo info
			if (count($this->geoArray) > 0 && $this->conf['geoip.']['addValuesToMarkerALL']) { // If geoip info should be added to marker All
				$geoAllArray = t3lib_div::trimExplode(',', $this->conf['geoip.']['addValuesToMarkerALL'], 1); // explode at ,
				if (count($geoAllArray) > 0) { // if array 
					foreach ($geoAllArray as $geokey => $geovalue) { // one loop for every geoinfo
						if ($this->geoArray[$geovalue]) { // if this key exists
							$markerArray['###POWERMAIL_LABEL###'] = $this->pi_getLL('geoip_' . $geovalue, ucfirst($geovalue));
							$markerArray['###POWERMAIL_VALUE###'] = $this->geoArray[$geovalue];
							$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'],$markerArray); // add line
						}
					}
				}
			}
			$subpartArray['###CONTENT###'] = $content_item; // ###POWERMAIL_ALL###
        }
        
        // add standard Markers
		$this->markerArray['###POWERMAIL_UPLOADFOLDER###'] = $this->conf['upload.']['folder']; // Relative upload folder from constants
		if (count($this->geoArray) > 0) foreach ($this->geoArray as $key => $value) $this->markerArray['###POWERMAIL_GEO_' . strtoupper($key) . '###'] = $this->geoArray[$key]; // Add standardmarker for geo info (ip, countryCode, countryName, region, city, zip, lng, lat, dmaCode, areaCode)
		$this->markerArray['###POWERMAIL_BASEURL###'] = ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // absolute path (baseurl)
		$this->markerArray['###POWERMAIL_ALL###'] = trim($this->cObj->substituteMarkerArrayCached($this->tmpl['all']['all'], array(), $subpartArray)); // Fill ###POWERMAIL_ALL###
        
		$this->markerArray['###POWERMAIL_THX_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_thanks'],$this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_thanks'],$this->markerArray) ); // Thx message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILRECIPIENT_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailreceiver'],$this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailreceiver'],$this->markerArray) ); // Email to receiver message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILSENDER_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailsender'],$this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailsender'],$this->markerArray) ); // Email to sender message with ###fields###
		
		if(isset($this->markerArray)) return $this->markerArray;
    }
    
    
    // Function GetLabelfromBackend() to get label to current field for emails and thx message
    function GetLabelfromBackend($name, $value = '') {
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions

		if (strpos($name,'uid') !== FALSE) { // $name like uid55
			$uid = str_replace('uid', '', $name); // remove uid from uid43 to get 43

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // GET title where fields.flexform LIKE <value index="vDEF">vorname</value>
				'tx_powermail_fields.title',
				'tx_powermail_fields LEFT JOIN tx_powermail_fieldsets ON tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid LEFT JOIN tt_content ON tt_content.uid = tx_powermail_fieldsets.tt_content',
				//$where_clause = 'tt_content.uid = '.($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']).' AND tx_powermail_fields.uid = '.$uid.' AND tx_powermail_fields.hidden = 0 AND tx_powermail_fields.deleted = 0'.tslib_cObj::enableFields('tt_content'),
				$where_clause = 'tx_powermail_fields.uid = ' . $uid,
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
			if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			if (!empty($row['title'])) { // if title was found
				return $this->div->parseFunc($row['title'], $this->cObj, $this->conf['label.']['parse']); // return title
			} else { // no label to field found (Countryzoneselect, etc...)
				return ''; // empty return
			}
			
		} else { // no uid55 so return $name
			return $name;
		}
    }
    
    
    // Function DynamicLocalLangMarker() to get automaticly a marker from locallang.xml (###LOCALLANG_BLABLA### from locallang.xml: locallangmarker_blabla
    function DynamicLocalLangMarker($array) {
        $string = $this->pi_getLL(strtolower($this->locallangmarker_prefix . $array[1]));
        if (isset($string)) return $string;
    }
	
	
	// Function getSession() loads values from current session (with or without hidden fields)
	function getSession($what) {
		// $what could be: recipient_mail, sender_mail, thx, confirmation, mandatory
		
		// config
		$allowhidden = t3lib_div::trimExplode(',', $this->conf['hiddenfields.']['show'], 1); // allow/disallow hidden fields
		
		// 1. get sessionarray
		$sessionArray = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey . '_' . ($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'])); // Get piVars from session
		
		// 2. manipulate session values via typoscript (if wanted)
		$sessionArray = $this->div->TSmanipulation($sessionArray, $what, $this->conf, $this->cObj); // manipulate values via typoscript
		
		// 3. delete hiddenfield from session array if should
		if (
			($what == 'recipient_mail' && !$allowhidden[0]) || // if current action is recipient_mail and hiddenfields are not allowed for this
			($what == 'sender_mail' && !$allowhidden[1]) || // if current action is sender_mail and hiddenfields are not allowed for this
			($what == 'thx' && !$allowhidden[2]) || // if current action is thx and hiddenfields are not allowed for this
			($what == 'confirmation' && !$allowhidden[3]) || // if current action is confirmation and hiddenfields are not allowed for this
			($what == 'mandatory' && !$allowhidden[4]) // if current action is mandatory and hiddenfields are not allowed for this
		) { 
			// Give me all hidden field of current page
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'uid',
				'tx_powermail_fields',
				$where_clause = 'pid = ' . $GLOBALS['TSFE']->id . ' AND formtype = "hidden"' . tslib_cObj::enableFields('tx_powermail_fields'),
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
			if ($res) { // If there is a result
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every uploadfield 
					if (!empty($sessionArray['uid' . $row['uid']])) { // if value exists in session to current hiddenfield
						unset($sessionArray['uid' . $row['uid']]); // delete hiddenfield from session array
					}
				}
			}
		}
			
		if (!empty($sessionArray)) return $sessionArray;
	}
	

	// Function hook_submit_changeEmail() to add a hook and change markers and html code
	function hook_markerArray() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MarkerArrayHook'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MarkerArrayHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_markerArrayHook($this->what, $this->geoArray, $this->markerArray, $this->sessiondata, $this->tmpl, $this); // Manipulate arrays and objects
			}
		}
	}	 // Function for processing custom markers before substituting the HTML-Templates of the items


	// Function hook_additional_marker allows to manipulate markers at the point of generating
	function hook_additional_marker(&$markerArray, &$formValues, &$k, &$v, $kv = false, $vv = false) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldMarkerArrayHook'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldMarkerArrayHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FieldMarkerArrayHook($markerArray, $formValues, $k, $v, $kv, $vv, $this); // Get new marker Array from other extensions
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_markers.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_markers.php']);
}

?>