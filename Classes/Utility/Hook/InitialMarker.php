<?php
namespace In2code\Powermail\Utility\Hook;

use In2code\Powermail\Utility\Div;

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
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class InitialMarker extends AbstractMarker {

	/**
	 * Initially fill the marker field from title
	 *
	 * @param string $status mode of change
	 * @param string $table the table which gets changed
	 * @param string $uid uid of the record
	 * @param array $fieldArray the updateArray
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $uid, &$fieldArray) {
		if ($table != 'tx_powermail_domain_model_fields') {
			return;
		}
		$markers = array_merge((array) $this->existingMarkers, (array) $this->marker);
		$this->makeUniqueValueInArray($markers);

			// set marker for new field
		if (isset($this->data['tx_powermail_domain_model_fields'][$uid]['marker']) || stristr($uid, 'NEW')) {
			if (isset($fieldArray['marker']) && empty($fieldArray['marker'])) {
				$fieldArray['marker'] = 'marker_' . Div::createRandomString(8, FALSE);
			}
			if (!empty($markers['_' . $uid])) {
				$fieldArray['marker'] = $markers['_' . $uid];
			}
		}

			// revise marker if related to a new page and not allowed
		if (!empty($markers['_' . $uid]) && $markers['_' . $uid] !== $this->marker['_' . $uid]) {
			$fieldArray['marker'] = $markers['_' . $uid];
		}

			// marker should be empty on localized fields
		if (!empty($fieldArray['sys_language_uid']) && $fieldArray['sys_language_uid'] > 0) {
			unset($fieldArray['marker']);
		}
	}
}