<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner <alexander.kellner@einpraegsam.net>
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


	// Function user_step() could be used as condition instead of globalVar in Backend
		// This won't work: [globalVar = GP:tx_powermail_pi1|mailID > 0]
		// This will work: [userFunc = user_stap()] // submitted form
		// This will work: [userFunc = user_step(2)] // user ist on step 2 if morestep form
	function user_powermail_step($id = 0) {
	    $piVars = t3lib_div::_GET('tx_powermail_pi1'); // get GET params from powermail
		
	    if (!$id) { // if no id given from outside
	        
			if (intval($piVars['mailID']) > 0) return true; // if there is a mailID, return true
	        else return false; // no mailID, return false
			
	    } else { // if there is an id given from outside
	        
			if (intval($piVars['multiple']) == $id) return true; // if multiple == given param, return true
	        else return false; // not given param, return false
			
	    }
	
	}
?>