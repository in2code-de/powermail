<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Alexander Kellner, Mischa Heißmann <alexander.kellner@einpraegsam.net, typo3.2008@heissmann.org>
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

// This class offers geoip datas if available from extension geoip
class tx_powermail_geoip extends tslib_pibase {

	var $extKey = 'powermail';
    var $scriptRelPath = 'pi1/class.tx_powermail_pi1.php'; // Path to pi1 to get locallang.xml from pi1 folder
	var $ipOverride = 0; //'83.236.183.130'; // set a hardcoded user ip (only for testing)
	
	
	// Main Function for inserting datas to other tables
	function main($conf) {
		$this->conf = $conf;
		$this->getGeo(); // Get geo info
		
		if (count($this->GEOinfos) > 0) return $this->GEOinfos;
	}
	
	// Get info from geoip extension
	function getGeo() {
		// use geo ip if loaded
		if (t3lib_extMgm::isLoaded('geoip')) {
			require_once( t3lib_extMgm::extPath('geoip').'/pi1/class.tx_geoip_pi1.php');
			$this->media = t3lib_div::makeInstance('tx_geoip_pi1');
			
			if ($this->conf['geoip.']['file']) { // only if file for geoip is set
				$this->media->init($this->conf['geoip.']['file']); // Initialize the geoip Ext
				$this->GEOinfos = $this->media->getGeoIP($this->ipOverride ? $this->ipOverride : t3lib_div::getIndpEnv('REMOTE_ADDR')); // get all the infos of current user ip
			}
		}
	
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_geoip.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_geoip.php']);
}

?>