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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_geoip.php'); // file for geo info

/**
 * Plugin 'tx_powermail_pi1' for the 'powermail' extension.
 *
 * @author	Alex Kellner (alexander.kellner@in2code.de)
 * @package	TYPO3
 * @subpackage	tx_powermail_markers
 */
class tx_powermail_markers extends tslib_pibase {

    public $extKey = 'powermail';
    public $scriptRelPath = 'pi1/class.tx_powermail_pi1.php';    // Path to pi1 to get locallang.xml from pi1 folder
    public $locallangmarker_prefix = 'locallangmarker_'; // prefix for automatic locallangmarker

    /**
	 * Function GetMarkerArray() to set global Markers for Emails and THX message and all other outputs
	 *
	 * @param	array		TypoScript configuration
	 * @param	array		Session values
	 * @param	array		Content Object
	 * @param	string		Kind of output (recipient_mail, confirmation, ...)
	 * @return	array		markerArray
	 */
	public function GetMarkerArray($conf, $sessionfields, $cObj, $what = '') {
		// Configuration
		$this->pi_loadLL();
		$this->conf = $conf;
		$this->cObj = $cObj;
		$this->what = $what;
		$this->geo = t3lib_div::makeInstance('tx_powermail_geoip'); // Instance with geo class
       	$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->geoArray = $this->geo->main($this->conf); // Get geoinfo array
        $this->markerArray['###POWERMAIL_ALL###'] = '';
        $content_item = '';
        $this->markerArray = array(); // init
        $this->sessiondata = $this->getSession($what); // fill variable with values from session
        switch ($what) {
            case 'confirmation':
            case 'recipient_mail':
            case 'sender_mail':
            case 'thx':
                unset($this->sessiondata['FILE']);
        }
        $this->notInMarkerAll = t3lib_div::trimExplode(',', $this->conf['markerALL.']['notIn'], 1); // choose which fields should not be listed in marker ###ALL### (ERROR is never allowed to be shown)
        $this->tmpl['all']['all'] = $this->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['all']), "###POWERMAIL_ALL###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->tmpl['all']['item'] = $this->cObj->getSubpart($this->tmpl['all']['all'], "###ITEM###"); // Load HTML Template: ALL (works on subpart ###POWERMAIL_ALL###)
		$this->hook_markerArray(); // adds hook
        $this->baseURL = ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // absolute path (baseurl)
        $this->uploadFolder = $this->conf['upload.']['folder']; // Relative upload folder from constants
        if (!!$this->conf['upload.']['useTitleAsUploadSubFolderName']) {
            $this->uploadFolder .= $this->cObj->data['tx_powermail_title'] . '/';
        }

		if (isset($this->sessiondata) && is_array($this->sessiondata)) {

			// sort session vars to match the order specified in backend
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'tx_powermail_fields.uid AS uid',
				'tx_powermail_fields LEFT JOIN tx_powermail_fieldsets ON tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid',
				'tx_powermail_fieldsets.tt_content = ' . intval($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']) . ' AND tx_powermail_fields.deleted = 0 AND tx_powermail_fields.hidden = 0 ',
				'',
				'tx_powermail_fieldsets.sorting,tx_powermail_fields.sorting'
			);
			
			if ($res !== false) {
                $orderedSessionData = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					if ($this->sessiondata['uid' . $row['uid']] != "") {
						$orderedSessionData['uid' . $row['uid']] = $this->sessiondata['uid' . $row['uid']];
                        if (isset($this->sessiondata['uid' . ($row['uid'] + 100000)])) { // handle session var with offset of 100000 as countryzone
                            $orderedSessionData['uid' . ($row['uid'] + 100000)] = $this->sessiondata['uid' . ($row['uid'] + 100000)];
                            unset($this->sessiondata['uid' . $row['uid'] + 100000]);
                        }
					}
                    unset($this->sessiondata['uid' . $row['uid']]);
				}
				$this->sessiondata = array_merge($orderedSessionData, $this->sessiondata);
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
			}

			$markerArray['###POWERMAIL_EVEN_ODD###'] = 'even';
			// normal markers
            foreach ($this->sessiondata as $k => $v) { // One loop for every piVar
                $this->label = $k;
                $this->setLabelAndType();
				if ($k == 'FILE' && count($v) > 1) { // only if min two files uploaded (don't show uploaded files two times if only one upload field)
				    $i = 1;
					foreach ($v as $key => $file) {
						$markerArray['###POWERMAIL_EVEN_ODD###'] = $markerArray['###POWERMAIL_EVEN_ODD###'] == 'even' ? 'odd' : 'even';
						$this->markerArray['###' . strtoupper($k) . '_' . $key . '###'] = stripslashes($this->div->nl2br2($file)); // Filename
						$this->markerArray['###LABEL_' . strtolower($k) . '_' . $key . '###'] = sprintf($this->pi_getLL('locallangmarker_confirmation_files','Attached file %s: '), $i); // Label to filename
						if (!in_array(strtoupper($k), $this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###', $this->notInMarkerAll)) {
							$markerArray['###POWERMAIL_LABEL###'] = sprintf($this->pi_getLL('locallangmarker_confirmation_files','Attached file %s: '), $i);
							$markerArray['###POWERMAIL_VALUE###'] = stripslashes($this->div->nl2br2($file));
                            if (!!$this->conf['upload.']['addLinkToUploads'] && $this->what == 'recipient_mail') {
                                $markerArray['###POWERMAIL_VALUE###'] = '<a href="' . $this->baseURL . $this->uploadFolder . $markerArray['###POWERMAIL_VALUE###'] . '" target="_blank">' . $markerArray['###POWERMAIL_VALUE###'] . '</a>';
                            }
						}
						$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v); // add hook
						$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray);
					$i ++;
					}
				}
				else {
					if (is_numeric(str_replace('uid', '', $k))) { // use only piVars like UID555
						if (!is_array($v)) { // standard: value is not an array
							if (is_numeric(str_replace('uid', '', $k))) { // check if key is like uid55
								$this->markerArray['###' . strtoupper($k) . '###'] = stripslashes($this->div->nl2br2($v)); // fill ###UID55###
								$this->markerArray['###LABEL_' . strtoupper($k) . '###'] = $this->label; // fill ###LABEL_UID55###

								// ###POWERMAIL_ALL###
								if (!in_array(strtoupper($k), $this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###', $this->notInMarkerAll)) {
									$markerArray['###POWERMAIL_LABEL###'] = $this->label;
									$markerArray['###POWERMAIL_VALUE###'] = t3lib_div::removeXSS(stripslashes($this->div->nl2br2($v))); // XSS Protection
									$markerArray['###POWERMAIL_EVEN_ODD###'] = $markerArray['###POWERMAIL_EVEN_ODD###'] == 'even' ? 'odd' : 'even';
                                    if (!!$this->conf['upload.']['addLinkToUploads'] && $this->type == 'file' && $this->what == 'recipient_mail') {
                                        $markerArray['###POWERMAIL_VALUE###'] = '<a href="' . $this->baseURL . $this->uploadFolder . $markerArray['###POWERMAIL_VALUE###'] . '" target="_blank">' . $markerArray['###POWERMAIL_VALUE###'] . '</a>';
                                    }
									$markerArray['###POWERMAIL_UID###'] = $k;
									$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v);
									if ($this->conf['markerALL.']['hideLabel'] == 1 && $markerArray['###POWERMAIL_VALUE###'] || $this->conf['markerALL.']['hideLabel'] == 0) { // if hideLabel on in backend: add only if value exists
										$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray); // add line
									}
								}
							}
						} else { // value is still an array (needed for e.g. checkboxes tx_powermail_pi1[uid55][0])
							$i = 0; // init counter
							$markerArray['###POWERMAIL_VALUE###'] = '';
							foreach ($v as $kv => $vv) { // One loop for every piVar
								if (is_numeric(str_replace('uid','',$k))) { // check if key is like uid55
									if ($vv) { // if value exists
										$this->markerArray['###' . strtoupper($k) . '_' . $kv . '###'] = stripslashes($this->div->nl2br2($vv)); // fill ###UID55_0###
										$this->markerArray['###' . strtoupper($k) . '###'] .= ($i != 0 ? $this->cObj->stdWrap($this->conf['field.']['checkboxSplitSign'], $this->conf['field.']['checkboxSplitSign.']) : '') . stripslashes($this->div->nl2br2($vv)); // fill ###UID55### (comma between every value)
										$this->markerArray['###LABEL_' . strtoupper($k) . '###'] = $this->label; // fill ###LABEL_UID55###
										
										// ###POWERMAIL_ALL###
										if (!in_array(strtoupper($k), $this->notInMarkerAll) && !in_array('###' . strtoupper($k) . '###', $this->notInMarkerAll)) {
											$markerArray['###POWERMAIL_LABEL###'] = $this->label;
											$markerArray['###POWERMAIL_VALUE###'] .= stripslashes($this->div->nl2br2($vv)) . ', ';
											$markerArray['###POWERMAIL_EVEN_ODD###'] = $markerArray['###POWERMAIL_EVEN_ODD###'] == 'even' ? 'odd' : 'even';
											$markerArray['###POWERMAIL_UID###'] = $k;
											$this->hook_additional_marker($markerArray, $this->sessiondata, $k, $v, $kv, $vv); // add hook
											//$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray);
										}
										$i ++; // increase counter
									}
								}
							}
							$markerArray['###POWERMAIL_VALUE###'] = rtrim($markerArray['###POWERMAIL_VALUE###'], ', ');
							$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray);
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
							$markerArray['###POWERMAIL_EVEN_ODD###'] = $markerArray['###POWERMAIL_EVEN_ODD###'] == 'even' ? 'odd' : 'even';
							$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['all']['item'], $markerArray); // add line
						}
					}
				}
			}
			$subpartArray['###CONTENT###'] = $content_item; // ###POWERMAIL_ALL###
        }

        // add standard Markers
		$this->markerArray['###POWERMAIL_UPLOADFOLDER###'] = $this->uploadFolder; // Relative upload folder
		if (count($this->geoArray) > 0) foreach ($this->geoArray as $key => $value) {
			$this->markerArray['###POWERMAIL_GEO_' . strtoupper($key) . '###'] = $this->geoArray[$key]; // Add standardmarker for geo info (ip, countryCode, countryName, region, city, zip, lng, lat, dmaCode, areaCode)
		}
		$this->markerArray['###POWERMAIL_BASEURL###'] = ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // absolute path (baseurl)
		$this->markerArray['###POWERMAIL_ALL###'] = trim($this->cObj->substituteMarkerArrayCached($this->tmpl['all']['all'], array(), $subpartArray)); // Fill ###POWERMAIL_ALL###

		$this->markerArray['###POWERMAIL_THX_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_thanks'],$this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_thanks'], $this->markerArray)); // Thx message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILRECIPIENT_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailreceiver'], $this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailreceiver'], $this->markerArray)); // Email to receiver message with ###fields###
        $this->markerArray['###POWERMAIL_EMAILSENDER_RTE###'] = ( $this->conf['rte.']['parse'] == 1 ? $this->pi_RTEcssText(tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailsender'], $this->markerArray)) : tslib_cObj::substituteMarkerArrayCached($this->cObj->data['tx_powermail_mailsender'], $this->markerArray)); // Email to sender message with ###fields###

		return $this->markerArray;
    }

    /**
	 * Function setLabelAndType() set label and field type to current field for emails and thx message
	 *
	 */
    private function setLabelAndType() {
        $this->type = 'undefined';
 		if (strpos($this->label, 'uid') !== false) { // if label like uid55
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
                'tx_powermail_fields.title, tx_powermail_fields.formtype',
                'tx_powermail_fields
                LEFT JOIN tx_powermail_fieldsets ON tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid
                LEFT JOIN tt_content ON tt_content.uid = tx_powermail_fieldsets.tt_content',
                'tx_powermail_fields.uid = ' . intval(str_replace('uid', '', $this->label))
            );
            if ($res !== false) {
                $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
                if ($row !== false) {
                    $this->label = $this->div->parseFunc($row['title'], $this->cObj, $this->conf['label.']['parse']); // set label to title
                    $this->type = $row['formtype']; // set type to formtype
                } else {
                    // if no result found, check for country zone select
                    $res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
                        'tx_powermail_fields.title, tx_powermail_fields.formtype',
                        'tx_powermail_fields
                        LEFT JOIN tx_powermail_fieldsets ON tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid
                        LEFT JOIN tt_content ON tt_content.uid = tx_powermail_fieldsets.tt_content',
                        'tx_powermail_fields.uid = ' . (intval(str_replace('uid', '', $this->label)) - 100000)
                    );
                    $row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);
                    if ($row2 !== false) {
                        $this->LOCAL_LANG_loaded = 0;
                        $this->pi_loadLL();
                        $this->label = sprintf($this->pi_getLL('country_zone_of', 'State of %s'), $this->div->parseFunc($row2['title'], $this->cObj, $this->conf['label.']['parse']));
                        $this->type = 'select';
                        $GLOBALS['TYPO3_DB']->sql_free_result($res2);
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
            }
        }
    }

    /**
	 * Function dynamicLocalLangMarker() to get automatically a marker from locallang.xml (###LOCALLANG_BLABLA### from locallang.xml: locallangmarker_blabla
	 *
	 * @param	array		Locallang array
	 * @return	string		Label from locallang
	 */
    private function dynamicLocalLangMarker($array) {
        $string = $this->pi_getLL(strtolower($this->locallangmarker_prefix . $array[1]));
        return $string;
    }

    /**
	 * Function getSession() loads values from current session (with or without hidden fields)
	 *
	 * @param	string		Kind of output
	 * @return	array		Session Array
	 */
	private function getSession($what) {
		// $what could be: recipient_mail, sender_mail, thx, confirmation, mandatory

		// config
		$allowhidden = t3lib_div::trimExplode(',', $this->conf['hiddenfields.']['show'], 1); // allow/disallow hidden fields

		// 1. get session array
		$sessionArray = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->extKey . '_' . ($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'])); // Get piVars from session

		// 2. manipulate session values via typoscript (if wanted)
		$sessionArray = $this->div->TSmanipulation($sessionArray, $what, $this->conf, $this->cObj); // manipulate values via typoscript

		// 3. delete hidden field from session array if should
		if (
			($what == 'recipient_mail' && !$allowhidden[0]) || // if current action is recipient_mail and hidden fields are not allowed for this
			($what == 'sender_mail' && !$allowhidden[1]) || // if current action is sender_mail and hidden fields are not allowed for this
			($what == 'thx' && !$allowhidden[2]) || // if current action is thx and hidden fields are not allowed for this
			($what == 'confirmation' && !$allowhidden[3]) || // if current action is confirmation and hidden fields are not allowed for this
			($what == 'mandatory' && !$allowhidden[4]) // if current action is mandatory and hidden fields are not allowed for this
		) {
			// Give me all hidden and deleted field of current page
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'uid',
				'tx_powermail_fields',
				'( pid = ' . intval($GLOBALS['TSFE']->id) . ' AND formtype = "hidden"' . tslib_cObj::enableFields('tx_powermail_fields') . ') OR ( pid = ' . intval($GLOBALS['TSFE']->id) . ' AND deleted = 1 )'
			);
			if ($res !== false) { // If there is a result
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every hidden or deleted field
					if (!empty($sessionArray['uid' . $row['uid']])) {
						unset($sessionArray['uid' . $row['uid']]); // unset all hidden or deleted session variables
					}
				}
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
		}

		return $sessionArray;
	}

    /**
	 * Function hook_submit_changeEmail() to add a hook and change markers and html code
	 *
	 * @return	void
	 */
	private function hook_markerArray() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MarkerArrayHook'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MarkerArrayHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_markerArrayHook($this->what, $this->geoArray, $this->markerArray, $this->sessiondata, $this->tmpl, $this); // Manipulate arrays and objects
			}
		}
	}

    /**
	 * Function hook_additional_marker allows to manipulate markers at the point of generating
	 *
	 * @param	array		Marker Array
	 * @param	array		Form Values
	 * @param	string		Key 1. Level
	 * @param	string		Value 1. Level
	 * @param	string		Key 2. Level
	 * @param	string		Value 2. Level
	 * @return	void
	 */
	private function hook_additional_marker(&$markerArray, &$formValues, &$k, &$v, &$kv = false, &$vv = false) {
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