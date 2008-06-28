<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Mischa Heißmann, Alexander Kellner <typo3.2008@heissmann.org, alexander.kellner@wunschtacho.de>
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

require_once(PATH_tslib.'class.tslib_pibase.php'); // get pibase
require_once('class.tx_powermail_html.php'); // get html and field functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_functions_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_dynamicmarkers.php'); // file for dynamicmarker functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_sessions.php'); // load session class


class tx_powermail_form extends tslib_pibase {
	var $prefixId      = 'tx_powermail_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_powermail_form.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'powermail'; // The extension key.
	var $pi_checkCHash = true;

	// Function main chooses what to show
	function main($content, $conf) {
		// config
		global $TSFE;
		$this->cObj = $TSFE->cObj; // cObject
		$this->conf = $conf;
		$this->baseurl = ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] ? $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] : 'http://'.$_SERVER['HTTP_HOST'].'/'); // set baseurl
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_powermail_dynamicmarkers'); // New object: TYPO3 marker function
		
		// load mandatory javascript in header if needed
		$js = '';
		if ($this->conf['field.']['checkboxJS'] == 1) $js .= $this->includeJavaScript("js/checkbox/", "checkbox.js"); // add file 1 (only if new checkbox variant with hidden fields was chosen via constants)
		if ($this->conf['js.']['Prototype'] == 1) $js .= $this->includeJavaScript("js/mandatoryjs/lib/","prototype.js"); // add file 2 (always because prototype.js is used with date2cal)
		if ($this->conf['js.']['mandatorycheck'] == 1) {
			$js .= $this->includeJavaScript("js/mandatoryjs/src/","effects.js"); // add file 3
			$js .= $this->includeJavaScript("js/mandatoryjs/","fabtabulous.js"); // add file 4
			
			// add dynamic file (current page with type=3131) // file 5
			if ($GLOBALS['TSFE']->tmpl->setup['config.']['simulateStaticDocuments'] != '1') { // simulatestaticdocuments is not activated
				$dynjslink = $this->pibase->cObj->typolink (
					'x', 
					array(
						'returnLast' => 'url',
						'parameter' => $GLOBALS['TSFE']->id,
						'additionalParams' => '&type=3131'.(intval(t3lib_div::GPvar('L'))>0?'&L='.t3lib_div::GPvar('L'):'')
					)
				);
			} else { // simulatestaticdocuments active
				$dynjslink = 'index.php?id='.$GLOBALS['TSFE']->id.'&type=3131'.(intval(t3lib_div::GPvar('L'))>0?'&L='.t3lib_div::GPvar('L'):''); // Link to JavaScript if ssd is active
			}
			$js .= "\t".'<script src="'.($this->conf['js.']['HTMLentities']==1 ? htmlentities($dynjslink) : $dynjslink).'" type="text/javascript"></script>'."\n";
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $js; // write to html header
		
		// what to show
		if ($this->pibase->cObj->data['tx_powermail_multiple'] == 2) { // If multiple (PHP) active (load tmpl_multiple.html)
			
			// Set limit
			$limitArray = array(0,1); // If multiple (PHP) set limit
			if(isset($this->piVars['multiple'])) $limitArray[0] = ($this->piVars['multiple'] - 1); // Set current fieldset
			$limit = $limitArray[0].','.$limitArray[1]; // e.g. 0,1
		
		} elseif ($this->pibase->cObj->data['tx_powermail_multiple'] == 1) { // If multiple (JS) active
			
			// add css for multiple javascript
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= "\t".'<link rel="stylesheet" type="text/css" href="'.str_replace('../','',t3lib_extMgm::extRelPath($this->extKey)).'css/multipleJS.css" />';
			$limit = ''; // no limit for SQL select
			
		} elseif ($this->pibase->cObj->data['tx_powermail_multiple'] == 0) { // Standardmode
			
			$limit = ''; // no limit for SQL select
			
		} else return 'Wrong multiple setting ('.$this->pibase->cObj->data['tx_powermail_multiple'].') in backend'; // Errormessage if wrong multiple choose
		
		return $this->form($limit); // Load only
	}
	
	
	// Function form() generates form tags and loads field
	function form($limit = '') {
		// Configuration
		$div_functions = t3lib_div::makeInstance('tx_powermail_functions_div'); // New object: div functions
		$html_input_field = t3lib_div::makeInstance('tx_powermail_html'); // New object: html generation of input fields
		$i=1; // counter for automatic tabindex

		$this->tmpl['all'] = tslib_cObj::fileResource($this->conf['template.']['formWrap']); // Load HTML Template
		$this->tmpl['formwrap']['all'] = $this->pibase->cObj->getSubpart($this->tmpl['all'],'###POWERMAIL_FORMWRAP###'); // work on subpart 1
		$this->tmpl['formwrap']['item'] = $this->pibase->cObj->getSubpart($this->tmpl['formwrap']['all'],'###POWERMAIL_ITEM###'); // work on subpart 2
		$this->tmpl['multiplejs']['all'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['MultipleJS']),'###POWERMAIL_MULTIPLEJS_PAGEBROWSER###'); // Load HTML Template for multiple JS (work on subpart)

		// Form tag generation
		$this->InnerMarkerArray = array(); $this->OuterMarkerArray = array(); $this->content_item = ''; // init
		$this->OuterMarkerArray['###POWERMAIL_TARGET###'] = htmlentities($this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"additionalParams"=>'&tx_powermail_pi1[mailID]='.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']),"useCacheHash"=>1))); // Fill Marker with action parameter
		$this->OuterMarkerArray['###POWERMAIL_NAME###'] = $this->pibase->cObj->data['tx_powermail_title']; // Fill Marker with formname
		$this->OuterMarkerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		$this->OuterMarkerArray['###POWERMAIL_FORM_UID###'] = ($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']); // Form method
		$this->OuterMarkerArray['###POWERMAIL_MANDATORY_JS###'] = $this->AddMandatoryJS();
		if($this->pibase->cObj->data['tx_powermail_multiple'] == 2) { // If multiple PHP is set
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_BACKLINK###'] = $this->multipleLink(-1); // Backward Link (-1)
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_FORWARDLINK###'] = $this->multipleLink(1); // Forward Link (+1)
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_PAGEBROWSER###'] = $this->multipleLink(0); // Pagebrowser
			if($this->multiple['numberoffieldsets'] != $this->multiple['currentpage']) { // On last fieldset, don't overwrite Target
				$this->OuterMarkerArray['###POWERMAIL_TARGET###'] = htmlentities($this->pibase->cObj->typolink('x',array("returnLast"=>"url","parameter"=>$GLOBALS['TSFE']->id,"additionalParams"=>'&tx_powermail_pi1[mailID]='.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).'&tx_powermail_pi1[multiple]='.($this->multiple['currentpage'] + 1),"useCacheHash"=>1))); // Overwrite Target
			}
		} elseif ($this->pibase->cObj->data['tx_powermail_multiple'] == 1) { // If multiple JS is set
			$this->OuterMarkerArray['###POWERMAIL_MULTIPLE_PAGEBROWSER###'] = $this->multipleLink('js'); // JavaScript switch
		}
		
		// UID of the last fieldset to current tt_content
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','tx_powermail_fieldsets','tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tx_powermail_fieldsets'),'','sorting DESC','1');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this->lastfieldset = $row['uid']; // uid of last fieldset to current tt_content (needed to show only on the last fieldset the captcha code)
		
		// Give me all needed fieldsets
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'uid,title',
			'tx_powermail_fieldsets',
			$where_clause = 'tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tx_powermail_fieldsets'),
			$groupBy = '',
			$orderBy = 'sorting ASC',
			$limit
		);
		if ($res1) { // If there is a result
			while($row_fs = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) { // One loop for every fieldset
				$this->InnerMarkerArray['###POWERMAIL_FIELDS###'] = ''; // init

				// Give me all fields in current fieldset, which are related to current content
				$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
					'fs.uid fs_uid,f.uid f_uid,fs.felder fs_fields,fs.title fs_title,f.title f_title,f.formtype f_type,f.flexform f_field,c.tx_powermail_title c_title,f.fe_field f_fefield',
					'tx_powermail_fieldsets fs LEFT JOIN tx_powermail_fields f ON (fs.uid = f.fieldset) LEFT JOIN tt_content c ON (fs.tt_content = c.uid)',
					$where_clause = 'fs.deleted = 0 AND fs.hidden = 0 AND fs.tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).' AND f.hidden = 0 AND f.deleted = 0 AND f.fieldset = '.$row_fs['uid'].$whereadd,
					$groupBy = '',
					$orderBy = 'fs.sorting, f.sorting',
					$limit1 = ''
				);
				if ($res2) { // If there is a result
					$html_input_field->init($this->conf,$this);
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) { // One loop for every field
						$this->InnerMarkerArray['###POWERMAIL_FIELDS###'] .= $html_input_field->main($conf, $row, $this->tabindexArray()); // Get HTML code for each field
						$i++; // increase counter
					}
				}
				
				$this->InnerMarkerArray['###POWERMAIL_FIELDSETNAME###'] = $row_fs['title']; // Name of fieldset
				$this->InnerMarkerArray['###POWERMAIL_FIELDSETNAME_small###'] = $div_functions->clearName($row_fs['title'],1,32); // Fieldsetname clear (strtolower = 1 / cut after 32 letters)
				$this->InnerMarkerArray['###POWERMAIL_FIELDSET_UID###'] = $row_fs['uid']; // uid of fieldset
				$this->content_item .= $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['formwrap']['item'],$this->InnerMarkerArray);
			}
		}

		$this->subpartArray = array('###POWERMAIL_CONTENT###' => $this->content_item); // work on subpart 3
		
		$this->hook(); // adds hook
		$this->contentForm = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['formwrap']['all'],$this->OuterMarkerArray,$this->subpartArray); // substitute Marker in Template
		$this->contentForm = $this->dynamicMarkers->main($this->conf, $this->pibase->cObj, $this->contentForm); // Fill dynamic locallang or typoscript markers
		$this->contentForm = preg_replace("|###.*?###|i","",$this->contentForm); // Finally clear not filled markers
		
		return $this->contentForm; // return HTML
	}
	
	
	// Function tabindexArray() returns array with sorted numbers for tabindex
	function tabindexArray() {
		// config
		$array = array(); //init
		
		// Let's go
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'tx_powermail_fields.uid, tx_powermail_fields.formtype, tx_powermail_fields.flexform',
			'tx_powermail_fields LEFT JOIN tx_powermail_fieldsets ON (tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid) LEFT JOIN tt_content ON (tx_powermail_fieldsets.tt_content = tt_content.uid)',
			$where_clause = 'tx_powermail_fieldsets.tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tx_powermail_fieldsets').tslib_cObj::enableFields('tx_powermail_fields'),
			$groupBy = '',
			$orderBy = 'tx_powermail_fieldsets.sorting, tx_powermail_fields.sorting',
			$limit = ''
		);
		if ($res) { // If there is a result
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every field
				if ($row['formtype'] != 'check' && $row['formtype'] != 'radio') { // if not checkbox or radiobuttons
					$array[] = $row['uid']; // increase array with this uid
				} else { // if checkbox or radiobuttons
					$options = t3lib_div::trimExplode("\n", $this->pi_getFFvalue(t3lib_div::xml2array($row['flexform']), 'options'), 1); // all options in an array
					
					for ($i=0; $i<count($options); $i++) { // one loop for every option
						$array[] = $row['uid'].'_'.$i; // increase array with this uid
					}
				}
			}
		}
		
		return $array;
	}
	
	
	// Function multipleLink() generates links to switch between fieldset-pages
	function multipleLink($add = 0) {
		// Get number of pages of current form
		$this->multiple = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'count(*) no',
			'tx_powermail_fieldsets',
			$where_clause = 'tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tx_powermail_fieldsets'),
			$groupBy = '',
			$orderBy = '',
			$limit
		);
		if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		// Configuration
		$this->multiple['numberoffieldsets'] = $row['no']; // Numbers of all fieldsets
		if(isset($this->piVars['multiple'])) $this->multiple['currentpage'] = $this->piVars['multiple']; // Currentpage
		else $this->multiple['currentpage'] = 1; // Currentpage = 1 if not set
		
		if ($add === 1) { // Forward link
		
			if($this->multiple['numberoffieldsets'] != $this->multiple['currentpage']) { // If current fieldset is not the latest
				$content = '<input type="submit" value="'.$this->pi_getLL('multiple_forward').'" class="tx_powermail_pi1_submitmultiple_forward" />';
			} else $content = ''; // clear it if it's not needed
			
		} elseif ($add === -1) { // Backward link
		
			if($this->multiple['currentpage'] > 1) { // If current fieldset is not the first
				$link = $this->baseurl.$this->pibase->cObj->typolink('x',array('parameter'=>$GLOBALS['TSFE']->id,'returnLast'=>'url', 'additionalParams'=>'&tx_powermail_pi1[multiple]='.($this->multiple['currentpage'] + $add).'&tx_powermail_pi1[mailID]='.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']),'useCacheHash' => 1)); // Create target url
				$content = '<input type="button" value="'.$this->pi_getLL('multiple_back').'" onclick="location=\''.$link.'\'" class="tx_powermail_pi1_submitmultiple_back" />';
			}
			else $content = ''; // clear it if it's not needed
		
		} elseif ($add === 0) { // show pagebrowser
			
			/*
			// e.g. page1 page2 page3 page4
			$content = '';			
			for($i=0;$i<$this->multiple['numberoffieldsets'];$i++) {
				if(($i+1) == $this->multiple['currentpage']) $classadd = ' powermail_bagebrowser_current'; else $classadd = '';
				$content .= '<a href="'.$this->pibase->cObj->typolink('x',array('parameter'=>$GLOBALS['TSFE']->id,'returnLast'=>'url', 'additionalParams'=>'&tx_powermail_pi1[multiple]='.($i + 1).'&tx_powermail_pi1[mailID]='.$this->pibase->cObj->data['uid'],'useCacheHash' => 1)).'" class="powermail_pagebrowser'.$classadd.'">Seite '.($i+1).'</a>'."\n";
			}
			*/
			
			// e.g. 3 of 8
			$content = $this->multiple['currentpage'].$this->pi_getLL('pagebrowser_inner').$this->multiple['numberoffieldsets']; // 1 of 4
			$content = $this->pibase->cObj->wrap($content,$this->conf['pagebrowser.']['wrap'],'|'); // wrap this
		
		} elseif ($add === 'js') { // Pagebrowser Multiple JS
			
			$this->tmpl['multiplejs']['item'] = $this->pibase->cObj->getSubpart($this->tmpl['multiplejs']['all'],'###POWERMAIL_ITEM###');
			$content_item = '';
			
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'uid,title',
				'tx_powermail_fieldsets',
				$where_clause = 'tt_content = '.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).tslib_cObj::enableFields('tx_powermail_fieldsets'),
				$groupBy = '',
				$orderBy = '',
				''
			);
			if ($res) { // If there is a result
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // One loop for every fieldset
					$markerArray['###POWERMAIL_MULTIPLEJS_PAGEBROWSER_LINK###'] = htmlentities($this->pibase->cObj->typolink('x',array('parameter'=>$GLOBALS['TSFE']->id, 'returnLast'=>'url', 'useCacheHash' => 1)).'#tx-powermail-pi1_fieldset_'.$row['uid']);             
 					$markerArray['###POWERMAIL_MULTIPLEJS_PAGEBROWSER_TITLE###'] = $row['title'];
					$content_item .= $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['multiplejs']['item'], $markerArray);
				}
				$subpartArray['###POWERMAIL_CONTENT###'] = $content_item; 
				$content = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['multiplejs']['all'], array(), $subpartArray);
				$content = $this->dynamicMarkers->main($this->conf, $this->pibase->cObj, $content); // Fill dynamic locallang or typoscript markers
				$content = preg_replace("|###.*?###|i","",$content); // Finally clear not filled markers
			}
		
		} else { // Error
		
			$content = 'ERROR in function multipleLink';
		
		}
		
		return $content;
	}
	
	
	// Function hook() to enable manipulation datas with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FormWrapMarkerHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_FormWrapMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_FormWrapMarkerHook($this->OuterMarkerArray,$this->subpartArray,$this->conf,$this); // Open function to manipulate datas
			}
		}
	}
	
	// Generates JavaScript HTML output
	function includeJavaScript($path,$file) {
		if($file) {
			$js = "\t".'<script src="'.t3lib_extMgm::siteRelPath($this->extKey).$path.$file.'" type="text/javascript"></script>'."\n";
			return $js;
		}
	}
	
	// Add Javascript after form output for mandatory check
	function AddMandatoryJS() {
		$js = '
			<script type="text/javascript">
				function formCallback(result, form) {
					window.status = "valiation callback for form \'" + form.id + "\': result = " + result;
				}
				var valid = new Validation(\''.$this->OuterMarkerArray['###POWERMAIL_NAME###'].'\', {immediate : true, onFormValidate : formCallback});
			</script>
		';
		
		if ($this->conf['js.']['Prototype'] == 1 && $this->conf['js.']['mandatorycheck'] == 1) {
			return $js; // return JavaScript
		} else {
			return ''; // return an empty string
		}
	}

	function init(&$conf,&$pibase) {
		$this->conf = $conf;
		$this->pibase = $pibase;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_form.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_form.php']);
}

?>