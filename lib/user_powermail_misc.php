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

// Function user_step() could be used as condition instead of globalVar in Backend
// This won't work: [globalVar = GP:tx_powermail_pi1|mailID > 0]
// This will work: [userFunc = user_powermail_step()] // submitted form
// This will work: [userFunc = user_powermail_step(2)] // user ist on step 2 if multiple step form
function user_powermail_step($id = 0) {
	$piVars = t3lib_div::_GP('tx_powermail_pi1'); // get params from powermail

	if (!$id) { // if no id given from outside
		if (intval($piVars['mailID']) > 0) return true; // if there is a mailID, return true
	} else { // if there is an id given from outside
		if (intval($piVars['multiple']) == $id) return true; // if multiple == given param, return true
	}

	return false;
}

?>