<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Alexander Kellner, Mischa Heißmann <alexander.kellner@wunschtacho.de, typo3@heissmann.org>
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
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_markers.php'); // file for marker functions
require_once(t3lib_extMgm::extPath('powermail').'lib/class.tx_powermail_dynamicmarkers.php'); // file for dynamicmarker functions

class tx_powermail_confirmation extends tslib_pibase {
	var $extKey        = 'powermail';	// The extension key.
	var $pi_checkCHash = true;

	function main($content,$conf){
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin
		
		// Instances
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_powermail_dynamicmarkers'); // New object: TYPO3 dynamicmarker function
		$this->markers = t3lib_div::makeInstance('tx_powermail_markers'); // New object: TYPO3 marker functions
		$this->markers->init($this->conf,$this); // Initialise the new instance to make cObj available in all other functions.
		
		// Template
		$this->tmpl = array();
		$this->tmpl['all'] = $this->pibase->cObj->getSubpart(tslib_cObj::fileResource($this->conf['template.']['confirmation']),'###POWERMAIL_CONFIRMATION###'); // Load HTML Template (work on subpart)
		
		// Fill Markers
		$this->markerArray = $this->markers->GetMarkerArray(); // Fill markerArray
		$this->markerArray['###POWERMAIL_NAME_BACK###'] = $this->pibase->cObj->data['tx_powermail_title'].'_confirmation_back'; // Fill Marker with formname
		$this->markerArray['###POWERMAIL_NAME_SUBMIT###'] = $this->pibase->cObj->data['tx_powermail_title'].'_confirmation_submit'; // Fill Marker with formname
		$this->markerArray['###POWERMAIL_METHOD###'] = $this->conf['form.']['method']; // Form method
		$this->markerArray['###POWERMAIL_TARGET_BACK###'] = $this->pibase->cObj->typolink('x',array('returnLast'=>'url','parameter'=>$GLOBALS['TSFE']->id,'useCacheHash'=>1)); // Create target url
		$this->markerArray['###POWERMAIL_TARGET_SUBMIT###'] = $this->pibase->cObj->typolink('x',array('returnLast'=>'url','parameter'=>$GLOBALS['TSFE']->id,'additionalParams'=>'&tx_powermail_pi1[mailID]='.($this->pibase->cObj->data['_LOCALIZED_UID'] > 0 ? $this->pibase->cObj->data['_LOCALIZED_UID'] : $this->pibase->cObj->data['uid']).'&tx_powermail_pi1[sendNow]=1','useCacheHash'=>1)); // Create target url
			
		// Return
		$this->hook(); // adds hook
		$this->content = $this->pibase->cObj->substituteMarkerArrayCached($this->tmpl['all'],$this->markerArray); // substitute Marker in Template
		$this->content = $this->dynamicMarkers->main($this->conf, $this->pibase->cObj, $this->content); // Fill dynamic locallang or typoscript markers
		$this->content = preg_replace("|###.*?###|i","",$this->content); // Finally clear not filled markers
		return $this->content; // return HTML
	}
	
	
	// Function hook() to enable manipulation data with another extension(s)
	function hook() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_ConfirmationHook'])) { // Adds hook for processing of extra global markers
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_ConfirmationHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_ConfirmationHook($this->markerArray,$this); // Open function to manipulate data
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



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_confirmation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/pi1/class.tx_powermail_confirmation.php']);
}

?>