<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner, Mischa Heißmann, <alexander.kellner@einpraegsam.net, typo3.YYYY@heissmann.org>
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
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_sessions.php'); // load session class
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_dynamicmarkers.php'); // file for dynamicmarker functions
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_removexss.php'); // file for removexss function class
require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_countryzones.php'); // file for countryzones function class
// extern resources
	// date2cal
if (t3lib_extMgm::isLoaded('date2cal', 0)) { // if date2cal is loaded
	if (file_exists(t3lib_extMgm::siteRelPath('date2cal') . 'src/class.jscalendar.php')) { // if file exists (date2cal 7.0.0 or newer)
		include_once(t3lib_extMgm::siteRelPath('date2cal') . 'src/class.jscalendar.php'); // include calendar class
	}
}



class tx_powermail_html extends tslib_pibase {
	
	var $prefixId      = 'tx_powermail_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_html.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;


	/**
	 * Function main() is checks which field has to be rendered and returns needed field
	 *
	 * @param	array	$conf: TS configuration
	 * @param	array	$sessionfields: values from session
	 * @param	obj		$cObj: content object
	 * @param	array	$row: values from db request (contains all values to current powermail field
	 * @param	array	$tabindex: array with tabindex
	 * @param	string	$counter
	 * @return	string	$content
	 */
	function main($conf, $sessionfields, $cObj, $row, $tabindex, $counter = 0) {
		// Config
		$this->conf = $conf;
		$this->sessionfields = $sessionfields;
		$this->cObj = $cObj;
		$this->xml = $row['f_field']; // get xml from flexform to current field
		$this->type = $row['f_type']; // get type of current field
		$this->formtitle = $row['c_title']; // get title of powermail
		$this->uid = $row['f_uid']; // get uid of current field
		$this->fe_field = $row['f_fefield']; // Get frontend user field if related to
		$this->description = $row['f_description']; // Get frontend user field if related to
		$this->class_f = $row['f_class']; // Get css class of current field
		$this->class_fs = $row['fs_class']; // Get css class of current fieldset
		$this->tabindex = $tabindex; // get current tabindex
		$this->counter = $counter; // counter for alternate function
		$this->pi_loadLL();
		$this->pi_initPIflexForm(); // allow flexform
		$this->tmpl = array('all' => tslib_cObj::fileResource($this->conf['template.']['fieldWrap'])); // Load HTML Template
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_powermail_dynamicmarkers'); // New object: TYPO3 marker function
		$this->removeXSS = t3lib_div::makeInstance('tx_powermail_removexss'); // New object: removeXSS function
		$this->div = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$this->title = $this->div->parseFunc($row['f_title'], $this->cObj, $this->conf['label.']['parse']); // get label to current field

		// Main functions
		$this->GetSessionValue(); // get value from session (if any)
		$this->setGlobalMarkers(); // set global markers
		$this->html_hook1(); // adds hook to manipulate some stuff

		// selection
		if ($this->type) { // If type exists
			switch($this->type) {
				case 'text':
					$this->content = $this->html_text(); // generate text field <input type="text"...
				break;
				case 'textarea':
					$this->content = $this->html_textarea(); // generate textarea <textarea...
				break;
				case 'check':
					$this->content = $this->html_check(); // generate textarea <input type="checkbox"
				break;
				case 'select':
					$this->content = $this->html_select(); // generate selectorbox <select><option>...
				break;
				case 'captcha':
					$this->content = $this->html_captcha(); // generate captcha request
				break;
				case 'radio':
					$this->content = $this->html_radio(); // generate radio buttons <input type="radio"...
				break;
				case 'submit':
					$this->content = $this->html_submit(); // generate submitbutton <input type="submit"...
				break;
				case 'reset':
					$this->content = $this->html_reset(); // generate resetbutton <input type="reset"...
				break;
				case 'label':
					$this->content = $this->html_label(); // generate textlabel
				break;
				case 'html':
					$this->content = $this->html_html(); // generate pure html
				break;
				case 'content':
					$this->content = $this->html_content(); // returns page content
				break;
				case 'file':
					$this->content = $this->html_file(); // generate file field
				break;
				case 'password':
					$this->content = $this->html_password(); // generate password field
				break;
				case 'hidden':
					$this->content = $this->html_hidden(); // generate hidden field
				break;
				case 'datetime':
					$this->content = $this->html_datetime(); // generate datetime field
				break;
				case 'date':
					$this->content = $this->html_date(); // generate date field
				break;
				case 'button':
					$this->content = $this->html_button(); // generate button field
				break;
				case 'submitgraphic':
					$this->content = $this->html_submitgraphic(); // generate submitgraphic button
				break;
				case 'countryselect':
					$this->content = $this->html_countryselect(); // generate select fields with countries from static_info_tables
				break;
				case 'typoscript':
					$this->content = $this->html_typoscript(); // gets typoscript element and output the result of it
				break;
				default: // default settings
					if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldHook'][$this->type])) { // Adds hook for processing of extra fields
						foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldHook'][$this->type] as $_classRef) {
							$_procObj = &t3lib_div::getUserObj($_classRef);
							$this->content = $_procObj->PM_FieldHook($this->xml, $this->title, $this->type, $this->uid, $this->markerArray, $this->piVarsFromSession, $this);
						}
					} else { // no hook so write errormessage
						$this->content = 'POWERMAIL: wrong input field required: <strong>' . $this->type . '</strong> in tx_powermail_pi1_html (field uid ' . $row['f_uid'] . ')<br />'; // errormessage
					}
				break;
			}
		} else { // no type selected
			$this->content = 'POWERMAIL: <strong>no field type</strong> in backend selected (field uid ' . $row['f_uid'] . ')<br />'; // errormessage
		}
		
		$this->html_hook2(); // adds hook to manipulate content before return
		if (!$this->div->subpartsExists($this->tmpl)) $this->content = $this->pi_getLL('error_templateNotFound', 'Template not found, check path to your powermail templates') . '<br />';
		
		if (isset($this->content)) return $this->content;
	}


	/**
	 * Function html_text() returns HTML tag for textfields
	 *
	 * @return	string	$content
	 */
	function html_text() {
		$this->tmpl['html_text'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_TEXT###'); // work on subpart
		
		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_text'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_textarea() returns HTML tag for textareas
	 *
	 * @return	string	$content
	 */
	function html_textarea() {
		$this->tmpl['html_textarea'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_TEXTAREA###'); // work on subpart

		$this->markerArray['###VALUE###'] = substr(trim($this->markerArray['###VALUE###']), 7, -1); // remove the first 7 letters (value=") and the last letter (")

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_textarea'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_select() returns HTML tag for selectorbox
	 *
	 * @return	string	$content
	 */
	function html_select() {
		$this->tmpl['html_select']['all'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_SELECT###'); // work on subpart 1
		$this->tmpl['html_select']['item'] = tslib_cObj::getSubpart($this->tmpl['html_select']['all'], '###ITEM###'); // work on subpart 2

		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options')) { // Only if options are set
			$content_item = ''; $options = $set = array(); // init
			$optionlines = t3lib_div::trimExplode("\n", $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options'), 1); // Every row is a new option
			for ($i=0; $i < count($optionlines); $i++) { // Every loop for every option
				$options[$i] = t3lib_div::trimExplode('|', $optionlines[$i], 0); // Every row is a new option
			}
			
			// preparing an array for preselection of multi fields
			if ($this->conf['prefill.']['uid' . $this->uid . '.']['selectedIndexes'] || is_array($this->conf['prefill.']['uid' . $this->uid . '.']['selectedIndexes.'])) { // if there are values in the array selectedIndexes.
				$selected = t3lib_div::intExplode(',', $this->cObj->stdWrap($this->conf['prefill.']['uid' . $this->uid . '.']['selectedIndexes'], $this->conf['prefill.']['uid' . $this->uid . '.']['selectedIndexes.']));
			} elseif ($this->conf['prefill.']['uid' . $this->uid . '.']['selectedValues'] || is_array($this->conf['prefill.']['uid' . $this->uid . '.']['selectedValues.'])) {
				$selected = t3lib_div::trimExplode(',', $this->cObj->stdWrap($this->conf['prefill.']['uid' . $this->uid . '.']['selectedValues'], $this->conf['prefill.']['uid' . $this->uid . '.']['selectedValues.']));
			} else {
				$selected = array();
			}


			for ($i=0; $i < count($optionlines); $i++) { // One tag for every option
				$markerArray['###LABEL###'] = $this->dontAllow($options[$i][0]); // fill label marker with label
				$markerArray['###VALUE###'] = $this->dontAllow(isset($options[$i][1]) ? $options[$i][1] : $options[$i][0]); // fill value marker with value
				
				// ###SELECTED###
				if (!is_array($this->piVarsFromSession['uid' . $this->uid])) { // no multiple
					if ($options[$i][2] == '*') $markerArray['###SELECTED###'] = ' selected="selected"'; // selected from backend
					else $markerArray['###SELECTED###'] = ''; // clear
					if (isset($this->piVarsFromSession['uid' . $this->uid])) { // if session was set
						if ($this->piVarsFromSession['uid' . $this->uid] == ($options[$i][1] ? $options[$i][1] : $options[$i][0])) $markerArray['###SELECTED###'] = 'selected="selected" '; // mark as selected
						else $markerArray['###SELECTED###'] = ''; // clear
					}
				} else { // multiple
					for ($j=0; $j<count($this->piVarsFromSession['uid' . $this->uid]); $j++) {
						if ($this->piVarsFromSession['uid' . $this->uid][$j] == ($options[$i][1] ? $options[$i][1] : $options[$i][0])) {
							$markerArray['###SELECTED###'] = ' selected="selected"'; // mark as selected
							$set[$i] = 1;
						}
					}
					if (!$set[$i]) $markerArray['###SELECTED###'] = ''; // clear
				}
				
				// Preselection from typoscript
				if (!$set[$i] && !empty($this->conf['prefill.'])) {
					if ($this->isPrefilled($i, $selected, ($options[$i][1] ? $options[$i][1] : $options[$i][0])) != false) {
						$markerArray['###SELECTED###'] = ' selected="selected"'; // mark as selected
					} else {
						$markerArray['###SELECTED###'] = ''; // clear
					}
				}


				$this->html_hookwithinfieldsinner($markerArray); // adds hook to manipulate the markerArray for any field
				$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['html_select']['item'], $markerArray);
			}
		}
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'multiple')) $this->markerArray['###NAME###'] = 'name="' . $this->prefixId . '[uid' . $this->uid . '][]" '; // overwrite name to markerArray like tx_powermail_pi1[55][]

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = $this->cObj->substituteMarkerArrayCached($this->tmpl['html_select']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_check() returns HTML tag for checkboxes
	 *
	 * @return	string	$content
	 */
	function html_check() {
		$this->tmpl['html_check']['all'] = $this->cObj->getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_CHECK' . ($this->conf['field.']['checkboxJS']==1 ? 'JS' : '') . '###'); // work on subpart 1 (###POWERMAIL_FIELDWRAP_HTML_CHECK### OR ###POWERMAIL_FIELDWRAP_HTML_CHECKJS###)
		$this->tmpl['html_check']['item'] = $this->cObj->getSubpart($this->tmpl['html_check']['all'], '###ITEM###'); // work on subpart 2

		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options')) { // Only if options are set
			$content_item = ''; $options = array(); // init
			$optionlines = t3lib_div::trimExplode("\n", $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options'), 1); // Every row is a new option
			for ($i=0; $i<count($optionlines); $i++) { // Every loop for every option
				$options[$i] = t3lib_div::trimExplode('|', $optionlines[$i], 0); // Every row is a new option
			}

			for ($i=0; $i < count($optionlines); $i++) { // One tag for every option
				$markerArray['###NAME###'] = 'name="' . $this->prefixId . '[uid' . $this->uid . '][' . $i . ']" '; // add name to markerArray
				$markerArray['###LABEL###'] = $this->dontAllow($this->div->parseFunc($options[$i][0], $this->cObj, $this->conf['label.']['parse'])); // add label
				$markerArray['###LABEL_NAME###'] = 'uid' . $this->uid . '_' . $i; // add labelname
				$markerArray['###ID###'] = 'id="uid' . $this->uid . '_' . $i . '" '; // add labelname
				$markerArray['###VALUE###'] = 'value="' . $this->dontAllow(isset($options[$i][1]) ? $options[$i][1] : $options[$i][0]) . '" '; // add value (take value after pipe symbol or all if no pipe: "red | rd")
				$markerArray['###CLASS###'] = 'class="'; // start class tag
				$markerArray['###CLASS###'] .= ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1 ? 'validate-one-required ' : ''); // add required class if needed
				$markerArray['###CLASS###'] .= 'powermail_' . $this->formtitle; // add form title
				$markerArray['###CLASS###'] .= ' powermail_' . $this->type; // add input type
				$markerArray['###CLASS###'] .= ' powermail_uid' . $this->uid; // add input uid
				$markerArray['###CLASS###'] .= ' powermail_subuid' . $this->uid . '_' . $i; // add input subuid
				$markerArray['###CLASS###'] .= ($this->class_f != '' ? ' ' . $this->class_f : ''); // add manual class
				$markerArray['###CLASS###'] .= '" '; // close tag
				$markerArray['###HIDDENVALUE###'] = 'value="' . $this->piVarsFromSession['uid' . $this->uid][$i] . '"'; // add value for hidden field to markerArray
				if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1) $markerArray['###MANDATORY_SYMBOL###'] = $this->cObj->wrap($this->conf['mandatory.']['symbol'], $this->conf['mandatory.']['wrap'],'|'); // add mandatory symbol if current field is a mandatory field
				$this->turnedtabindex[$this->uid . '_' . $i] !== '' ? $markerArray['###TABINDEX###'] = 'tabindex="' . ($this->turnedtabindex[$this->uid . '_' . $i] + 1) . '" ' : $markerArray['###TABINDEX###'] = ''; // tabindex for every checkbox
				isset($this->newaccesskey[$this->uid][$i]) ? $markerArray['###ACCESSKEY###'] = 'accesskey="' . $this->newaccesskey[$this->uid][$i] . '" ' : $markerArray['###ACCESSKEY###'] = ''; // accesskey for every checkbox
				
				// ###CHECKED###
				if ($options[$i][2] == '*')  {
				    
					$markerArray['###CHECKED###'] = 'checked="checked" '; // checked from backend
    				$markerArray['###HIDDENVALUE###'] = 'value="' . $this->dontAllow(isset($options[$i][1]) ? $options[$i][1] : $options[$i][0]) . '" '; // add value for hidden field to markerArray
    			
				} elseif (!empty($this->conf['prefill.']['uid' . $this->uid . '_' . $i])) { // prechecking with typoscript for current field enabled
					
					if ($this->cObj->cObjGetSingle($this->conf['prefill.']['uid' . $this->uid . '_' . $i], $this->conf['prefill.']['uid' . $this->uid . '_' . $i . '.']) == 1) {
						$markerArray['###CHECKED###'] = 'checked="checked" '; // checked from backend
						$markerArray['###HIDDENVALUE###'] = 'value="' . $this->dontAllow(isset($options[$i][1]) ? $options[$i][1] : $options[$i][0]) . '" '; // add value for hidden field to markerArray
					} else $markerArray['###CHECKED###'] = ''; // clear
					
				}
				// AST end
				else $markerArray['###CHECKED###'] = ''; // clear
				if (isset($this->piVarsFromSession['uid' . $this->uid])) { // Preselection from session
					if (isset($this->piVarsFromSession['uid' . $this->uid][$i]) && $this->piVarsFromSession['uid' . $this->uid][$i] != '') {
					    $markerArray['###CHECKED###'] = 'checked="checked" '; // mark as checked
        				$markerArray['###HIDDENVALUE###'] = 'value="' . $this->piVarsFromSession['uid' . $this->uid][$i] . '"'; // add value for hidden field to markerArray
        			}

					else $markerArray['###CHECKED###'] = ''; // clear
				}
				
				$this->html_hookwithinfieldsinner($markerArray); // adds hook to manipulate the markerArray for any field
				$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['html_check']['item'], $markerArray); // substitute Marker in Template (subpart 2)
 			}

		}
		$subpartArray = array(); // init
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3
		
		// Outer Marker array
		$this->markerArray['###LABEL_MAIN###'] = $this->title; 
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid;

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = $this->cObj->substituteMarkerArrayCached($this->tmpl['html_check']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_radio() returns HTML tag for radio buttons
	 *
	 * @return	string	$content
	 */
	function html_radio() {
		$this->tmpl['html_radio']['all'] = $this->cObj->getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_RADIO###'); // work on subpart 1
		$this->tmpl['html_radio']['item'] = $this->cObj->getSubpart($this->tmpl['html_radio']['all'], '###ITEM###'); // work on subpart 2

		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options')) { // Only if options are set
			$content_item = ''; $options = array(); // init
			$optionlines = t3lib_div::trimExplode("\n", $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'options'), 1); // Every row is a new option
			for ($i=0;$i<count($optionlines);$i++) { // Every loop for every option
				$options[$i] = t3lib_div::trimExplode('|', $optionlines[$i], 0); // To split: label | value | *
			}

			for($i=0;$i<count($optionlines);$i++) { // One tag for every option
				$markerArray['###NAME###'] = 'name="' . $this->prefixId . '[uid' . $this->uid . ']" '; // add name to markerArray
				$markerArray['###LABEL###'] = $this->dontAllow($this->div->parseFunc($options[$i][0], $this->cObj, $this->conf['label.']['parse'])); // add label
				$markerArray['###LABEL_NAME###'] = 'uid' . $this->uid . '_' . $i; // add labelname
				$markerArray['###ID###'] = 'id="uid' . $this->uid . '_' . $i . '" '; // add labelname
				$markerArray['###VALUE###'] = 'value="' . $this->dontAllow(isset($options[$i][1]) ? $options[$i][1] : $options[$i][0]) . '" '; // add labelname
				//$markerArray['###CLASS###'] = 'class="'. ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1 ? 'validate-one-required' : '') .' powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_uid'.$this->uid.' powermail_subuid'.$this->uid.'_'.$i.'" '; // add class name to markerArray
				$markerArray['###CLASS###'] = 'class="'; // start class tag
				$markerArray['###CLASS###'] .= ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1 ? 'validate-one-required ' : ''); // add required class if needed
				$markerArray['###CLASS###'] .= 'powermail_' . $this->formtitle; // add form title
				$markerArray['###CLASS###'] .= ' powermail_' . $this->type; // add input type
				$markerArray['###CLASS###'] .= ' powermail_uid' . $this->uid; // add input uid
				$markerArray['###CLASS###'] .= ' powermail_subuid' . $this->uid . '_' . $i; // add input subuid
				$markerArray['###CLASS###'] .= ($this->class_f != '' ? ' ' . $this->class_f : ''); // add manual class
				$markerArray['###CLASS###'] .= '" '; // close tag
				$this->turnedtabindex[$this->uid . '_' . $i] !== '' ? $markerArray['###TABINDEX###'] = 'tabindex="' . ($this->turnedtabindex[$this->uid . '_' . $i] + 1) . '" ' : $markerArray['###TABINDEX###'] = ''; // tabindex for every radiobutton
				isset($this->newaccesskey[$this->uid][$i]) ? $markerArray['###ACCESSKEY###'] = 'accesskey="' . $this->newaccesskey[$this->uid][$i] . '" ' : $markerArray['###ACCESSKEY###'] = ''; // accesskey for every radiobutton
				
				// ###CHECKED###
				if ($options[$i][2] == '*') { // Preselection from backend
					$markerArray['###CHECKED###'] = 'checked="checked" '; // precheck radiobutton
				} else $markerArray['###CHECKED###'] = ''; // clear
				if (isset($this->piVarsFromSession['uid' . $this->uid])) { // Preselection from session
					if ($this->piVarsFromSession['uid' . $this->uid] == ($options[$i][1] ? $options[$i][1] : $options[$i][0])) { // mark as selected
						$markerArray['###CHECKED###'] = 'checked="checked" '; // precheck radiobutton
					} else $markerArray['###CHECKED###'] = ''; // clear
				}
				
				$this->html_hookwithinfieldsinner($markerArray); // adds hook to manipulate the markerArray for any field
				$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['html_radio']['item'], $markerArray); // substitute Marker in Template (subpart 2)
 			}


		}
		$subpartArray = array(); // init
		$subpartArray['###CONTENT###'] = $content_item; // subpart 3
		$this->markerArray['###LABEL_MAIN###'] = $this->title;
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid;
		
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->cObj->wrap($this->conf['mandatory.']['symbol'], $this->conf['mandatory.']['wrap'], '|'); // add mandatory symbol if current field is a mandatory field

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = $this->cObj->substituteMarkerArrayCached($this->tmpl['html_radio']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		
		return $content; // return HTML
	}


	/**
	 * Function html_submit() returns HTML tag for submit button
	 *
	 * @return	string	$content
	 */
	function html_submit() {
		$this->tmpl['html_submit'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_SUBMIT###'); // work on subpart
		
		// add class name to markerArray
		$this->markerArray['###CLASS###'] = 'class="powermail_' . $this->formtitle; // add formname
		$this->markerArray['###CLASS###'] .= ' powermail_' . $this->type; // add type
		$this->markerArray['###CLASS###'] .= ' powermail_submit_uid' . $this->uid; // add field uid
		$this->markerArray['###CLASS###'] .= ($this->class_f != '' ? ' ' . $this->class_f : ''); // Add manual class
		$this->markerArray['###CLASS###'] .= '" '; // close tag
		$this->markerArray['###VALUE###'] = 'value="' . $this->dontAllow($this->title) . '" '; // add value (used from title) to markerArray

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_submit'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_reset() returns HTML tag for reset button
	 *
	 * @return	string	$content
	 */
	function html_reset() {
			
		$this->tmpl['html_reset'] = tslib_cObj::getSubpart($this->tmpl['all'],'###POWERMAIL_FIELDWRAP_HTML_RESET###'); // work on subpart
		
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'clearSession')) { // if checkbox clearSession is checked
			$this->markerArray['###JS###'] = 'onclick="location=\'' . (strpos($GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'], 'http://') !== false ? '' : t3lib_div::getIndpEnv('TYPO3_SITE_URL')) . $this->cObj->typolink('x',array('returnLast'=>'url', 'parameter'=>$GLOBALS['TSFE']->id, 'additionalParams'=>'&tx_powermail_pi1[clearSession]=-1'), 1) . '\'" '; // Fill marker ###JS### with eventhandler
		}
		
		$this->markerArray['###CLASS###'] = 'class="powermail_' . $this->formtitle; // add formname
		$this->markerArray['###CLASS###'] .= ' powermail_' . $this->type; // add type
		$this->markerArray['###CLASS###'] .= ' powermail_submit_uid' . $this->uid; // add field uid
		$this->markerArray['###CLASS###'] .= ($this->class_f != '' ? ' ' . $this->class_f : ''); // Add manual class
		$this->markerArray['###CLASS###'] .= '" '; // close tag
		$this->markerArray['###VALUE###'] = 'value="'.$this->dontAllow($this->title).'" '; // add value (used from title) to markerArray

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_reset'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i' ,'', $content); // Finally clear not filled markers
		
		return $content; // return HTML
	}


	/**
	 * Function html_label() returns HTML tag for some text
	 *
	 * @return	string	$content
	 */
	function html_label() {
        $this->tmpl['html_label'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_LABEL###'); // work on subpart

        // ###CONTENT###
        if (isset($this->piVarsFromSession['uid' . $this->uid])) { // 1. if value is in piVars
            $this->markerArray['###CONTENT###'] = $this->dontAllow(stripslashes($this->div->nl2nl2($this->piVarsFromSession['uid' . $this->uid]))); // value from piVars
        } elseif ($this->fe_field && $GLOBALS['TSFE']->fe_user->user[$this->fe_field]) { // 2. if value should be filled from current logged in user
            $this->markerArray['###CONTENT###'] = $this->dontAllow(strip_tags($GLOBALS['TSFE']->fe_user->user[$this->fe_field])); // add value to markerArray if should filled from feuser data
        } elseif ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value')) { // 3. take value from backend (default value)
            $this->markerArray['###CONTENT###'] = strip_tags($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'), $this->conf['label.']['allowTags']);
        } elseif (!empty($this->conf['prefill.']['uid' . $this->uid])) { // 4. prefilling with typoscript for current field enabled
            $this->markerArray['###CONTENT###'] = $this->cObj->cObjGetSingle($this->conf['prefill.']['uid' . $this->uid], $this->conf['prefill.']['uid' . $this->uid . '.']); // add typoscript value
        } else { // 5. no prefilling - so clear value marker
            $this->markerArray['###CONTENT###'] = ''; // clear
        }
        //$this->markerArray['###CONTENT###'] = strip_tags($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'), $this->conf['label.']['allowTags']); // fill label marker

        // add hidden field
        if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'send')) { // label should be send with email
            $this->markerArray['###HIDDEN###'] = '<input type="hidden" name="' . $this->prefixId . '[uid' . $this->uid . ']" value="' . $this->div->clearValue($this->markerArray['###CONTENT###']) . '" />'; // create hidden field
        }

        $this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
        $content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_label'], $this->markerArray); // substitute Marker in Template
        $content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
        $content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
        return $content; // return HTML
    }


	/**
	 * Function html_html() returns pure HTML
	 *
	 * @return	string	$content
	 */
	function html_html() {
		$this->tmpl['html_html'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_HTML###'); // work on subpart
		
		/*
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'send')) { // label should be send with email
			$this->markerArray['###HIDDEN###'] = '<input type="hidden" name="'.$this->prefixId.'['.$this->div->clearName($this->title,1).']" value="'.$this->div->clearValue($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')).'" />'; // create hidden field
		}
		*/
		$this->markerArray['###CONTENT###'] = ($this->conf['html.']['removeXSS'] == 1 ? $this->removeXSS->RemoveXSS($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value')) : $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value')); // fill label marker (with or without removeXSS)

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_html'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_content() returns page content
	 *
	 * @return	string	$content
	 */
	function html_content() {
		$this->tmpl['html_content'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_CONTENT###'); // work on subpart

		$uid = str_replace('tt_content_', '', $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value')); // get uid from flexform
		$conf = array( // config
			'tables' => 'tt_content', 
			'source' => $uid,
			'dontCheckPid' => 1
		);
		$this->markerArray = array('###CONTENT###' => $this->cObj->RECORDS($conf)); // CONTENT Marker with content
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_content'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_password() returns password field like
	 *
	 * @return	string	$content
	 */
	function html_password() {
		$this->tmpl['html_password'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_PASSWORD###'); // work on subpart

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_password'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_file() returns file field
	 *
	 * @return	string	$content
	 */
	function html_file() {
		if (!$this->piVarsFromSession['uid' . $this->uid]) { // There is no uploaded file in the session
			$this->tmpl['html_file'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_FILE###'); // work on subpart
		
		} else { // There is an uploaded file in the session
			$this->tmpl['html_file'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_FILE_LIST###'); // work on subpart
			
			$this->markerArray['###FILE###'] = $this->piVarsFromSession['uid' . $this->uid];
			$this->markerArray['###DELETEFILE_URL###'] = $this->pi_linkTP_keepPIvars_url(array('clearSession' => $this->uid));
			$this->markerArray['###DELETEFILE###'] .= t3lib_extMgm::siteRelPath('powermail') . 'img/icon_del.gif';
			
		}
	
		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_file'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		
		if (!empty($content)) return $content; // return HTML
	}
	

	/**
	 * Function html_hidden() returns hidden field
	 *
	 * @return	string	$content
	 */
	function html_hidden() {
		$this->tmpl['html_hidden'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_HIDDEN###'); // work on subpart

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_hidden'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}


	/**
	 * Function html_datetime() returns text field for date and time with calender help
	 *
	 * @return	string	$content
	 */
	function html_datetime() {
		
		if (t3lib_extMgm::isLoaded('date2cal', 0)) { // only if date2cal is loaded
			$this->tmpl['html_datetime'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_DATETIME###'); // work on subpart
			if (file_exists(t3lib_extMgm::siteRelPath('date2cal') . 'src/class.jscalendar.php')) { // search for class.jscalendar.php (only available if date2cal version > 7.0.0)
			
				// Set value
				if (intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')) != 0 && $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'))
					$value = strftime($this->conf['format.']['datetime'], intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'))); // add value to markerArray
				if ($this->fe_field && $GLOBALS['TSFE']->fe_user->user[$this->fe_field])
					$value = strftime($this->conf['format.']['datetime'], $this->dontAllow(strip_tags($GLOBALS['TSFE']->fe_user->user[$this->fe_field]))); // add value to markerArray if should filled from feuser data
				if (isset($this->piVarsFromSession['uid' . $this->uid]))
					$value = $this->dontAllow($this->div->nl2nl2($this->piVarsFromSession['uid' . $this->uid])); // Overwrite value from session value
		
				// init jscalendar class
				$JSCalendar = JSCalendar::getInstance();
				$JSCalendar->setConfigOption('ifFormat', $this->conf['format.']['datetime']);
				$JSCalendar->setConfigOption('daFormat', $this->conf['format.']['datetime']);
				$JSCalendar->setDateFormat(true);
				$JSCalendar->setInputField('uid' . $this->uid);
				#$this->markerArray['###FIELD###'] .= $JSCalendar->render($value, 'tx_powermail_pi1[uid' . $this->uid . ']');
				$params = array(
					'checkboxField' => array(
						'name' => 'tx_powermail_pi1[uid' . $this->uid . ']'
					),
					'inputField' => array(
						'name' => 'tx_powermail_pi1[uid' . $this->uid . ']',
						'tabindex' => $this->turnedtabindex[$this->uid] + 1
					)
				);
				if ($this->markerArray['###ACCESSKEY###'] != '') { // if there is a defined accesskey
					$params['inputField']['accesskey'] = $this->accesskeyarray[$i][2]; // set accesskey for datefield
				}
				$this->markerArray['###FIELD###'] .= $JSCalendar->render($value, $params);
	
				// get initialisation code of the calendar
				if (($jsCode = $JSCalendar->getMainJS()) != '') $GLOBALS['TSFE']->additionalHeaderData['powermail_date2cal'] = $jsCode;
		
				$this->markerArray['###LABEL###'] = $this->title; // add label
				$this->markerArray['###LABEL_NAME###'] = 'uid' . $this->uid . '_hr'; // add name for label
				$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker
				if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->cObj->wrap($this->conf['mandatory.']['symbol'], $this->conf['mandatory.']['wrap'], '|'); // add mandatory symbol if current field is a mandatory field
		
				$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
				$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_datetime'], $this->markerArray); // substitute Marker in Template
				$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
				$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
			
			} else { // date2cal version too old
				$content = 'Installed <strong>date2cal</strong> extension is too old, please use min. version 7.0.0 of <a href="http://typo3.org/extensions/repository/view/date2cal/current/" title="date2cal in the TYPO3 repository" target="_blank">date2cal</a><br />';
			}
		
		} else { // Extension date2cal is missing
			$content = 'Please install extension <a href="http://typo3.org/extensions/repository/view/date2cal/current/" title="date2cal in the TYPO3 repository" target="_blank">date2cal</a> to use datetime feature<br />';
		}
		
		return $content; // return HTML
	}
	

	/**
	 * Function html_date() returns text field for date with calender help
	 *
	 * @return	string	$content
	 */
	function html_date() {
		
		if (t3lib_extMgm::isLoaded('date2cal', 0)) { // only if date2cal is loaded
			if (file_exists(t3lib_extMgm::siteRelPath('date2cal') . 'src/class.jscalendar.php')) { // search for class.jscalendar.php (only available if date2cal version > 7.0.0)
				
				$this->tmpl['html_date'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_DATE###'); // work on subpart
				
				// Set value
				if (intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value')) != 0 && $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'))
					$value = strftime($this->conf['format.']['date'], intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'))); // add value to markerArray
				if ($this->fe_field && $GLOBALS['TSFE']->fe_user->user[$this->fe_field])
					$value = strftime($this->conf['format.']['date'], $this->dontAllow(strip_tags($GLOBALS['TSFE']->fe_user->user[$this->fe_field]))); // add value to markerArray if should filled from feuser data
				if (isset($this->piVarsFromSession['uid' . $this->uid]))
					$value = $this->dontAllow($this->div->nl2nl2($this->piVarsFromSession['uid' . $this->uid])); // Overwrite value from session value
				
				// init jscalendar class
				$JSCalendar = JSCalendar::getInstance();
				$JSCalendar->setConfigOption('ifFormat', $this->conf['format.']['date']);
				$JSCalendar->setConfigOption('daFormat', $this->conf['format.']['date']);
				$JSCalendar->setInputField('uid' . $this->uid);
				#$this->markerArray['###FIELD###'] .= $JSCalendar->render($value, 'tx_powermail_pi1[uid' . $this->uid . ']');
				$params = array(
					'checkboxField' => array(
						'name' => 'tx_powermail_pi1[uid' . $this->uid . ']'
					),
					'inputField' => array(
						'name' => 'tx_powermail_pi1[uid' . $this->uid . ']',
						'tabindex' => $this->turnedtabindex[$this->uid] + 1
					)
				);
				if ($this->markerArray['###ACCESSKEY###'] != '') { // if there is a defined accesskey
					$params['inputField']['accesskey'] = $this->accesskeyarray[$i][2]; // set accesskey for datefield
				}
				$this->markerArray['###FIELD###'] .= $JSCalendar->render($value, $params);
				
				// get initialisation code of the calendar
				if (($jsCode = $JSCalendar->getMainJS()) != '') {
					$GLOBALS['TSFE']->additionalHeaderData['powermail_date2cal'] = $jsCode;			
				}
		
				$this->markerArray['###LABEL###'] = $this->title; // add label
				$this->markerArray['###LABEL_NAME###'] = 'uid' . $this->uid . '_hr'; // add name for label
				$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // UID to marker
				if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1) $this->markerArray['###MANDATORY_SYMBOL###'] = $this->cObj->wrap($this->conf['mandatory.']['symbol'], $this->conf['mandatory.']['wrap'], '|'); // add mandatory symbol if current field is a mandatory field
		
				$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
				$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_date'], $this->markerArray); // substitute Marker in Template
				$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
				$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
			
			} else { // date2cal version too old
				$content = 'Installed <strong>date2cal</strong> extension is too old, please use min. version 7.0.0 of <a href="http://typo3.org/extensions/repository/view/date2cal/current/" title="date2cal in the TYPO3 repository" target="_blank">date2cal</a><br />';
			}
		
		} else { // Extension date2cal is missing
			$content = 'Please install extension <a href="http://typo3.org/extensions/repository/view/date2cal/current/" title="date2cal in the TYPO3 repository" target="_blank">date2cal</a> to use date feature<br />';
		}
		
		return $content; // return HTML
	}


	/**
	 * Function html_button() returns button field
	 *
	 * @return	string	$content
	 */
	function html_button() {
		$this->tmpl['html_button'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_BUTTON###'); // work on subpart

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_button'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}
	

	/**
	 * Function html_countryselect() returns select field with countries from static_info_tables
	 *
	 * @return    [type]        ...
	 */
	function html_countryselect() {
	
		if (t3lib_extMgm::isLoaded('static_info_tables', 0)) { // only if static_info_tables is loaded
			// config
			$this->tmpl['html_countryselect']['all'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_COUNTRYSELECT###'); // work on subpart 1
			$this->tmpl['html_countryselect']['item'] = tslib_cObj::getSubpart($this->tmpl['html_countryselect']['all'], '###ITEM###'); // work on subpart 2
			$valuearray = $longvaluearray = array();
			$localfield = $whereadd = $content_item = ''; 
			$sort = 'cn_short_en'; // sort for a field
	
			// Filter for some countries
			if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'within')) { // if some countries for include where selected
				$whereadd = ' AND uid IN (' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'within')  .')'; // whereadd for within values
			} elseif ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'without')) { // if some country for exclude where selected
				$whereadd = ' AND uid NOT IN (' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'without') . ')'; //  whereadd for eclude values
			}
	
			// Look for another lang version (maybe static_info_tables_de or _fr)
			if ($GLOBALS['TSFE']->tmpl->setup['config.']['language']) { // if language was set in ts
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( mysql_query('DESCRIBE static_countries cn_short_' . $GLOBALS['TSFE']->tmpl->setup['config.']['language']) ); // check for localized version of static_info_tables
			}
			if ($row['Field']) { // if there is a localized version of static_info_tables
				$localfield = ', cn_short_' . $GLOBALS['TSFE']->tmpl->setup['config.']['language'] . ' cn_short_lang'; // add to query
				$sort = 'cn_short_lang'; // change sort
			} 
	
			// Give me all needed fields from static_info_tables
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'uid, cn_iso_2, cn_short_local, cn_short_en' . $localfield,
				'static_countries',
				$where_clause = '1' . $whereadd,
				$groupBy = '',
				$orderBy = $sort,
				$limit = ''
			);
			if ($res) { // If there is a result
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every country
					if ($row['cn_short_lang']) $row['cn_short_lang'] = $this->div->charset($row['cn_short_lang'], $this->conf['countryselect.']['charset']); // change charset of value
					$row['cn_short_en'] = $this->div->charset($row['cn_short_en'], $this->conf['countryselect.']['charset']); // change charset of value
	
					// Fill markers
					$markerArray['###VALUE###'] = $this->dontAllow($row['cn_iso_2']);
					$markerArray['###LONGVALUE###'] = ($row['cn_short_lang'] ? $this->dontAllow($row['cn_short_lang']) : $this->dontAllow($row['cn_short_en']));
	
					// Preselection
					if ($row['uid'] == $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'preselect') && $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'preselect') > 0) $markerArray['###SELECTED###'] = ' selected="selected"'; // preselect one country
					else $markerArray['###SELECTED###'] = '';
					if (isset($this->piVarsFromSession['uid' . $this->uid])) { // if there is a session entry
						if ($this->piVarsFromSession['uid' . $this->uid] == $row['cn_iso_2'] || $this->piVarsFromSession['uid' . $this->uid] == $row['cn_short_lang'] || $this->piVarsFromSession['uid' . $this->uid] == $row['cn_short_en']) { // check for short or long value
							$markerArray['###SELECTED###'] = ' selected="selected"'; // preselect one country
						} else $markerArray['###SELECTED###'] = '';
					}
	
					$this->html_hookwithinfieldsinner($markerArray); // adds hook to manipulate the markerArray for any field
					$content_item .= $this->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['item'], $markerArray);
				}
			}
	
			$subpartArray['###CONTENT###'] = $content_item; // subpart 3
	
			$this->countryzones = t3lib_div::makeInstance('tx_powermail_countryzones');
			$this->countryzones->preflight($this->uid, $this->xml, $this->markerArray, $this->tmpl, $this->formtitle, $this->conf, $this->piVarsFromSession, $this->cObj);
	
			$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
			$content = $this->cObj->substituteMarkerArrayCached($this->tmpl['html_countryselect']['all'], $this->markerArray, $subpartArray); // substitute Marker in Template
			$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
			$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
	
		} else { // Extension static_info_tables is missing
			$content = 'Please install extension <strong>static_info_tables</strong> to use countryselect feature';
		}
	
		return $content; // return HTML
	}

	
	/**
	 * Function html_captcha() returns captcha request
	 *
	 * @return	string	$content
	 */
	function html_captcha() {
		if (t3lib_extMgm::isLoaded('captcha',0) || t3lib_extMgm::isLoaded('sr_freecap',0) || t3lib_extMgm::isLoaded('jm_recaptcha',0) || t3lib_extMgm::isLoaded('wt_calculating_captcha',0)) { // only if a captcha extension is loaded
			$this->tmpl['html_captcha'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_CAPTCHA###'); // work on subpart
			
			if (t3lib_extMgm::isLoaded('sr_freecap', 0) && $this->conf['captcha.']['use'] == 'sr_freecap') { // use sr_freecap if available
				
				require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php'); // include freecap class
				$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2'); // new object
				$freecaparray = $this->freeCap->makeCaptcha(); // array with freecap marker
				
				$this->markerArray['###POWERMAIL_CAPTCHA_PICTURE###'] = $freecaparray['###SR_FREECAP_IMAGE###']; // captcha image
				$this->markerArray['###POWERMAIL_CAPTCHA_PICTURERELOAD###'] = $freecaparray['###SR_FREECAP_CANT_READ###']; // reload image button
				$this->markerArray['###POWERMAIL_CAPTCHA_ACCESSIBLE###'] = $freecaparray['###SR_FREECAP_ACCESSIBLE###']; // audio output
				$this->markerArray['###LABEL###'] = $this->title; // captcha label
				$this->markerArray['###POWERMAIL_CAPTCHA_DESCRIPTION###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'label'); // add captcha description to the marker
			
			} elseif (t3lib_extMgm::isLoaded('captcha', 0) && $this->conf['captcha.']['use'] == 'captcha') { // use captcha if available
			
				$this->markerArray['###POWERMAIL_CAPTCHA_PICTURE###'] = '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="" class="powermail_captcha powermail_captcha_captcha" />'; // captcha image
				$this->markerArray['###LABEL###'] = $this->title; // captcha label
				$this->markerArray['###POWERMAIL_CAPTCHA_DESCRIPTION###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'label'); // add captcha description to the marker
			
			} elseif (t3lib_extMgm::isLoaded('jm_recaptcha', 0) && $this->conf['captcha.']['use'] == 'recaptcha') { // use recaptcha if available
				
				$this->tmpl['html_captcha'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_RECAPTCHA###'); // work on subpart
				
				require_once(t3lib_extMgm::extPath('jm_recaptcha') . 'class.tx_jmrecaptcha.php'); // include recaptcha class
				$recaptcha = t3lib_div::makeInstance('tx_jmrecaptcha'); // new object
				
				$this->markerArray['###POWERMAIL_CAPTCHA_PICTURE###'] = $recaptcha->getReCaptcha(); // get captcha
				$this->markerArray['###LABEL###'] = $this->title; // captcha label
				$this->markerArray['###POWERMAIL_CAPTCHA_DESCRIPTION###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'label'); // add captcha description to the marker
			
			} elseif (t3lib_extMgm::isLoaded('wt_calculating_captcha', 0) && $this->conf['captcha.']['use'] == 'wt_calculating_captcha') { // use wt_calculating_captcha if available
				
				require_once(t3lib_extMgm::extPath('wt_calculating_captcha').'class.tx_wtcalculatingcaptcha.php'); // include captcha class
				$captcha = t3lib_div::makeInstance('tx_wtcalculatingcaptcha'); // generate object
				
				$this->markerArray['###POWERMAIL_CAPTCHA_PICTURE###'] = $captcha->generateCaptcha(); // image return
				$this->markerArray['###LABEL###'] = $this->title; // captcha label
				$this->markerArray['###POWERMAIL_CAPTCHA_DESCRIPTION###'] = $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'label'); // add captcha description to the marker
				
			} else return sprintf($this->pi_getLL('error_captchaWrongExt', 'Powermail ERROR: The chosen captcha extension "%s" is not loaded! Choose another captcha extension in the powermail constants or install the extension "%s".'), $this->conf['captcha.']['use'], $this->conf['captcha.']['use']);
			
			$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
			$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_captcha'], $this->markerArray); // substitute Marker in Template
			$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
			$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
			return $content; // return HTML
		
		} else { // Extension static_info_tables is missing
			$content = $this->pi_getLL('error_captchaNoExtFound', 'Please install a captcha extension like captcha, sr_freecap, jm_recaptcha or wt_calculating_captcha');
		}
		
		return $content;
	}
	

	/**
	 * Function html_graphicsubmit() returns graphic as submitbutton
	 *
	 * @return	string	$content
	 */
	function html_submitgraphic() {
		$this->tmpl['html_submitgraphic'] = tslib_cObj::getSubpart($this->tmpl['all'], '###POWERMAIL_FIELDWRAP_HTML_SUBMITGRAPHIC###'); // work on subpart
		
		$this->markerArray['###SRC###'] = 'src="' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'src') . '" '; // source path for image
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'alt')) $this->markerArray['###ALT###'] = 'alt="' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'alt') . '" '; // if alt text exist, write alt text

		$this->html_hookwithinfields(); // adds hook to manipulate the markerArray for any field
		$content = tslib_cObj::substituteMarkerArrayCached($this->tmpl['html_submitgraphic'], $this->markerArray); // substitute Marker in Template
		$content = $this->dynamicMarkers->main($this->conf, $this->cObj, $content); // Fill dynamic locallang or typoscript markers
		$content = preg_replace('|###.*?###|i', '', $content); // Finally clear not filled markers
		return $content; // return HTML
	}
	
	
	/**
	 * Function html_typoscript() returns result of a typoscript
	 *
	 * @return	string	$content
	 */
	function html_typoscript() {
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'typoscriptobject') != '') { // only if object field was set
			// config
			$str = array(); $array = array(); // init
			$tsarray = t3lib_div::trimExplode('.', $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'typoscriptobject'), 1); // $tsarray[0] = lib // $tsarray[1] = object
				
			// let's go		
			for ($i=0; $i<count($tsarray); $i++) { // One loop for every level in typoscript object array
				$str[0] .= '[\'' . str_replace(';', '', $tsarray[$i]) . ($i==(count($tsarray)-1) ? '' : '.') . '\']'; // create php code for array like ['lib.']['object']
				$str[1] .= '[\'' . str_replace(';', '', $tsarray[$i]) . '.\']'; // create php code for array like ['lib.']['object.']
			}
			eval("\$array[0] = \$GLOBALS['TSFE']->tmpl->setup$str[0];"); // $newarray = $array['lib.']['object']
			eval("\$array[1] = \$GLOBALS['TSFE']->tmpl->setup$str[1];"); // $newarray = $array['lib.']['object.']
			
			
			$localCObj = t3lib_div::makeInstance('tslib_cObj');
			$row = array ( // $row for using .field in typoscript
				'uid' => $this->uid, // make current field uid available
				'label' => $this->dontAllow($this->title), // make current label available
				'ttcontent_uid' => $this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'] // make current tt_content uid available
			);
			// $this->cObj->start($row, 'tx_powermail_fields'); // enable .field to use uid and label in typoscript
			// $content = $this->cObj->cObjGetSingle($array[0], $array[1]); // parse typoscript
			$localCObj->start($row, 'tx_powermail_fields'); // enable .field to use uid and label in typoscript
			$content = $localCObj->cObjGetSingle($array[0], $array[1]); // parse typoscript
		}
		if (!empty($content)) return $content;
	}
	
	
	
	################################################################################################################
	




	/**
	* Function setGlobalMarkers() to fill global markers with values
	*
	* @return    void
	*/
	function setGlobalMarkers() {

		// set global markers
		$this->markerArray = array(); // init
		
		// ###NAME###
		$this->markerArray['###NAME###'] = 'name="' . $this->prefixId . '[uid' . $this->uid . ']" '; // add name to markerArray like tx_powermail_pi1[55]
		
		// ###LABEL_NAME###
		$this->markerArray['###LABEL_NAME###'] = 'uid' . $this->uid; // add label name to markerArray
		
		// ###ID###
		$this->markerArray['###ID###'] = 'id="uid' . $this->uid . '" '; // add id to markerArray
		
		// ###CLASS###
		$this->required = '';
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'mandatory') == 1 || $this->type == 'captcha') $this->required = 'required '; // add class="required" if javascript mandatory should be activated and in captcha fields
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'validate') != '' && $this->type == 'text') $this->required .= $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'validate') . ' '; // add another key in class if javascript mandatory should be activated
		// class="required powermail_title powermail_text powermail_uid12"
		$this->markerArray['###CLASS###'] = 'class="'; // open tag
		$this->markerArray['###CLASS###'] .= $this->required; // if required class for JS
		$this->markerArray['###CLASS###'] .= 'powermail_' . $this->formtitle; // add formtitle
		$this->markerArray['###CLASS###'] .= ' powermail_' . $this->type; // add type of field
		$this->markerArray['###CLASS###'] .= ' powermail_uid' . $this->uid; // add uid of field
		$this->markerArray['###CLASS###'] .= ($this->class_f != '' ? ' ' . $this->class_f : ''); // Add manual class
		$this->markerArray['###CLASS###'] .= '" '; // close tag
		//$this->markerArray['###CLASS###'] = 'class="'.$this->required.'powermail_'.$this->formtitle.' powermail_'.$this->type.' powermail_uid'.$this->uid.'" '; // add class name to markerArray
		
		// ###SIZE###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'size')) { // if size is set in flexform
			$this->markerArray['###SIZE###'] = ($this->conf['input.']['style'] == 1 ? 'style="width: ' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'size')) . 'px;" ' : 'size="' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'size')) . '" '); // add size to markerArray
		}
		
		// ###COLS###
		// ###ROWS###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'cols') || $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'rows')) { // if rows or cols was set
			if ($this->conf['input.']['style'] == 1) { // if style should be used instead of rows and cols tags
				$this->markerArray['###COLS###'] = ''; // clear COLS marker
				$this->markerArray['###ROWS###'] = 'style="width: ' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'cols')) . 'px; height: ' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'rows')) . 'px;" '; // like style="width: 44px; height: 23px;"
			} else { // rows and cols tags should be used
				if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'cols')) { // if there is a value in the cols field
					$this->markerArray['###COLS###'] = 'cols="' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'cols')) . '" '; // add number of columns to markerArray
				}
				if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'rows')) { // if there is a value in the rows field
					$this->markerArray['###ROWS###'] = 'rows="' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'rows')) . '" '; // add number of rows to markerArray
				}
			}
		}
		
		// ###MULTIPLE###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'multiple')) { // if there is value in the multiple field
			$this->markerArray['###MULTIPLE###'] = 'multiple="multiple"'; // add multiple to markerArray
		}
		
		// ###MAXLENGTH###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'maxlength')) { // if there is value in the maxlength field
			$this->markerArray['###MAXLENGTH###'] = 'maxlength="' . intval($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'maxlength')) . '" '; // add size to markerArray
		}
		
		// ###READONLY###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'readonly')) { // if there is value in the readonly field
			$this->markerArray['###READONLY###'] = 'readonly="readonly" '; // add readonly to markerArray
		}
		
		// ###VALUE###
		if (isset($this->piVarsFromSession['uid' . $this->uid])) { // 1. if value is in session
			$this->markerArray['###VALUE###'] = 'value="' . $this->dontAllow(stripslashes($this->div->nl2nl2($this->piVarsFromSession['uid' . $this->uid]))) . '" '; // value from session value
		} elseif ($this->fe_field && $GLOBALS['TSFE']->fe_user->user[$this->fe_field]) { // 2. else if value should be filled from current logged in user
			$this->markerArray['###VALUE###'] = 'value="' . $this->dontAllow(strip_tags($GLOBALS['TSFE']->fe_user->user[$this->fe_field])) . '" '; // add value to markerArray if should filled from feuser data
		} elseif ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'value')) { // 3. take value from backend (default value)
			$this->markerArray['###VALUE###'] = 'value="' . $this->dontAllow(strip_tags($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value'))) . '" '; // add value to markerArray (don't allow html/php tags)
		} elseif (!empty($this->conf['prefill.']['uid' . $this->uid])) { // 4. prefilling with typoscript for current field enabled
			$this->markerArray['###VALUE###'] = 'value="' . $this->cObj->cObjGetSingle($this->conf['prefill.']['uid' . $this->uid], $this->conf['prefill.']['uid' . $this->uid . '.']) . '" '; // add typoscript value
		} else { // 5. no prefilling - so clear value marker
			$this->markerArray['###VALUE###'] = 'value="" '; // clear
		}
		
		// ###LABEL###
		if (!empty($this->title)) $this->markerArray['###LABEL###'] = $this->dontAllow($this->title); // add label to markerArray
		
		// ###DESCRIPTION###
		if (!empty($this->description)) $this->markerArray['###DESCRIPTION###'] = $this->cObj->wrap($this->dontAllow($this->description), $this->conf['description.']['wrap'], '|'); // add wrapped label to markerArray
		
		// ###MANDATORY_SYMBOL###
		if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml),'mandatory') == 1 || $this->type == 'captcha') $this->markerArray['###MANDATORY_SYMBOL###'] = $this->cObj->wrap($this->conf['mandatory.']['symbol'], $this->conf['mandatory.']['wrap'], '|');
		
		// ###POWERMAIL_FIELD_UID###
		$this->markerArray['###POWERMAIL_FIELD_UID###'] = $this->uid; // add uid to markerArray
		
		// ###POWERMAIL_TARGET###
		#$this->markerArray['###POWERMAIL_TARGET###'] = $GLOBALS['TSFE']->absRefPrefix.$this->cObj->typolink('x', array("returnLast" => "url", "parameter" => $GLOBALS['TSFE']->id, "useCacheHash"=>1)); // Global marker with form target
		$this->markerArray['###POWERMAIL_TARGET###'] = $this->cObj->typolink('x', array('returnLast' => 'url', 'parameter' => $GLOBALS['TSFE']->id, 'additionalParams' => '&tx_powermail_pi1[mailID]=' . ($this->cObj->data['_LOCALIZED_UID'] > 0 ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid']), 'useCacheHash' => 1));
		
		// ###POWERMAIL_NAME###
		$this->markerArray['###POWERMAIL_NAME###'] = $this->formtitle; // Global Marker with formname
		
		// ###ALTERNATE###
		$this->markerArray['###ALTERNATE###'] = ($this->div->alternate($this->counter) ? 'odd' : 'even'); // Fill class with "odd" or "even"
		
		// ###TABINDEX###
		// 1. add tabindex automaticly
		if (in_array($this->uid, $this->tabindex)) { // if current uid within tabindex array
			$this->turnedtabindex = array_flip($this->tabindex); // array flipped (values and keys)
			$this->markerArray['###TABINDEX###'] = 'tabindex="' . ($this->turnedtabindex[$this->uid] + 1) . '" '; // add tabindex automaticly
		}
		// 2. set tabindex from ts
		if (!empty($this->conf['barrier-free.']['tabindex'])) { // If manually set tabindex in ts
			$this->tabindex = t3lib_div::trimExplode(',', str_replace('uid', '', $this->conf['barrier-free.']['tabindex']), 1); // Array with uids
			$this->turnedtabindex = array_flip($this->tabindex); // array flipped (values and keys)
			if (in_array($this->uid, $this->tabindex)) { // If current uid exists in tabindex settings from ts
				$this->markerArray['###TABINDEX###'] = 'tabindex="' . ($this->turnedtabindex[$this->uid] + 1) . '" '; // add tabindex automaticly
			}
		}
		
		// ###ACCESSKEY###
		if (!empty($this->conf['barrier-free.']['accesskey'])) { // If manually set accesskey in ts
			$array = t3lib_div::trimExplode(',', $this->conf['barrier-free.']['accesskey'], 1); // Array with uids and subuids
			
			for ($i=0; $i<count($array); $i++) { // one loop for every uid/accesskey part
				$temparray[$i] = t3lib_div::trimExplode(':', $array[$i], 1); // Split on :
				$this->accesskeyarray[$i] = t3lib_div::trimExplode('_', str_replace('uid', '', $temparray[$i][0]), 1); // split on _
				$this->accesskeyarray[$i][2] = $temparray[$i][1]; // [2] = accesskey value
				unset($temparray); // delete array
				if ($this->accesskeyarray[$i][1] != '') $this->newaccesskey[$this->accesskeyarray[$i][0]][$this->accesskeyarray[$i][1]] = $this->accesskeyarray[$i][2]; // array for radiobuttons and checkboxes
				
				if ($this->uid == intval(str_replace('uid', '', $this->accesskeyarray[$i][0])) && !isset($this->accesskeyarray[$i][1])) { // accesskey to this uid found
					$this->markerArray['###ACCESSKEY###'] = 'accesskey="' . $this->accesskeyarray[$i][2] . '" '; // add accesskey to normal fields (first level)
				}
			}
		}
		
		// ###ONCHANGE###
		if ($this->conf['js.']['onchange']) {
			$this->markerArray['###ONCHANGE###'] = 'onchange="this.form.submit()"'; // onchange js for select fields
		}
		
		// ###ONFOCUS### Marker
		if ($this->conf['js.']['init'] || $this->conf['js.']['onfocus']) { // only allowed if jsinit or onfocus set
			if ($this->conf['js.']['init'] && 1==0) { // if jsinit allowed (currently deactivated!!!)

				$init =  'init(\'' . $this->prefixId . '[uid' . $this->uid . ']' . '\',\'uid' . $this->uid . '\',\'' .
					$this->pi_linkTP_keepPIvars_url (
						$overrulePIvars = array('basket' => '1'),
						$cache=0,
						$clearAnyway=0,
						$altPageId=0
					) . '\',\'\');'; // add js init to string like: onblur="init(tx_powermail_pi1[textfeld],textfeld,index.php?id=index,'');"

			} else $init = ''; // clean $init

			if ($this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value') && $this->conf['js.']['onfocus'] && !$this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'readonly')) { // if value exists
				$js = 'onfocus="if (this.value==\'' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value') . '\') this.value=\'\';" onblur="if (this.value==\'\') this.value=\'' . $this->pi_getFFvalue(t3lib_div::xml2array($this->xml), 'value') . '\'; ' . $init . '" '; // add onfocus js to markerArray
			} else { // if no value or focus not allowed
				if ($init) $js = 'onblur="' . $init . '" ';
				else $js = '';
			}
			$this->markerArray['###ONFOCUS###'] = $js; // Fill markerArray with JS
		}
		if (!isset($this->markerArray['###ONFOCUS###'])) $this->markerArray['###ONFOCUS###'] = '';
	}


	/**
	* Function GetSessionValue() to get any field value which is already in the session
	*
	* @return    void
	*/
	function GetSessionValue() {
		$this->sessions = t3lib_div::makeInstance('tx_powermail_sessions');
		$this->piVarsFromSession = $this->sessions->getSession($this->conf, $this->cObj, 0);
	}


	/**
	* Function html_hook1() to add a hook to manipulate some content
	*
	* @return	void
	*/
	function html_hook1() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook1'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook1'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FieldWrapMarkerHook1($this->uid, $this->xml, $this->type, $this->title, $this->markerArray, $this->piVarsFromSession, $this); // Get new marker Array from other extensions
			}
		}
	}


	/**
	* Function html_hook2() to add a hook at the end of this file to manipulate content
	*
	* @return	void
	*/
	function html_hook2() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FieldWrapMarkerHook($this->uid, $this->xml, $this->type, $this->title, $this->markerArray, $this->content, $this->piVarsFromSession, $this); // Get new marker Array from other extensions
			}
		}
	}


	/**
	* Function html_hookwithinfields() to add a hook in every field generation to manipulate markerArray
	*
	* @return	void
	*/
	function html_hookwithinfields() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerArrayHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerArrayHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FieldWrapMarkerHook($this->uid, $this->xml, $this->type, $this->title, $this->markerArray, $this->piVarsFromSession, $this); // Get new marker Array from other extensions
			}
		}
	}


	/**
	* Function html_hookwithinfieldsinner($markerArray) to add a hook in every field generation to manipulate markerArray in the inner loop (checkboxes, radiobuttons, etc..)
	*
	* @param	$markerArray: markerArray
	* @return	void
	*/
	function html_hookwithinfieldsinner(&$markerArray) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerArrayHookInner'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FieldWrapMarkerArrayHookInner'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FieldWrapMarkerHookInner($this->uid, $this->xml, $this->type, $this->title, $markerArray, $this->piVarsFromSession, $this); // Get new marker Array from other extensions
			}
		}
	}


	/**
	* Function dontAllow() removes not allowed signs from html tags (obsolete function)
	*
	* @return    $string
	*/
	function dontAllow($string) {
		return $string;
		#return str_replace(array('"'), '', $string); // return value without don't allowed signs
	}
	
	
	
	/**
	* Function isPrefilled returns whether a option is prefilled or not
	*
	* @param  	int		current index
	* @param  	array	array of values or indexes to select/check
	* @param  	value	current value
	*
	* @return	boolean
	*/
	function isPrefilled($index, $selected, $value) {
		if ($this->cObj->stdWrap($this->conf['prefill.']['uid' . $this->uid . '_' . $index], $this->conf['prefill.']['uid' . $this->uid . '_' . $index . '.'])) {
			return true; // by field
		} elseif (is_int($selected[0]) && in_array($index, (array) $selected)) {
			return true; // by index
		} elseif (in_array($value, (array) $selected)) {
			return true; // by value
		}
		
		return false; // default
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_html.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_html.php']);
}

?>