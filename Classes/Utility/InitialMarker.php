<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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


/**
 * Class to extend Pi1 field marker e.g. {firstname}
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Powermail_Utility_InitialMarker {

	/**
	 * Initialy filling of marker field
	 *
	 * @param	string		$status mode of change
	 * @param	string		$table the table which gets changed
	 * @param	string		$id uid of the record
	 * @param	array		$fieldArray the updateArray
	 * @param	array		$this obj
	 * @return	an updated fieldArray()
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj) {
		if ($table != 'tx_powermail_domain_model_fields' || $status != 'new') { // stop if not powermail field table
			return $fieldArray;
		}
		if (!empty($fieldArray['marker'])) { // stop if marker field is already filled
			return $fieldArray;
		}
		$fieldArray['marker'] = $this->cleanString($fieldArray['title']);
	}

	/**
	 * Clean Marker String ("My Field" => "my_field")
	 *
	 * @param	string		$string Any String
	 * @return	string
	 */
	private function cleanString($string) {
		$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
		$string = str_replace(array('-'), '_', $string);
		$string = strtolower($string);
		return $string;
	}
}
?>