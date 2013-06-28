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
 * This class could called with AJAX via eID and returns a location from geo coordinates
 *
 * @author	Alex Kellner <alexander.kellner@in2code.de>, in2code.
 * @package	TYPO3
 * @subpackage	eidGetLocation
 */
class eidGetLocation {

	/**
	 * Generates the output
	 *
	 * @return string		from action
	 */
	public function main() {
		$lat = t3lib_div::_GP('lat');
		$lng = t3lib_div::_GP('lng');

		$address = $this->getAddressFromGeo($lat, $lng);
		return $address['route'] . ' ' . $address['street_number'];
	}

	/**
	 * Get Address from geo coordinates
	 *
	 * @param float $lat
	 * @param float $lng
	 * @return array all location infos
	 * 		['street_number'] = 12;
	 * 		['route'] = 'Kunstmuehlstr.';
	 * 		['locality'] = 'Rosenheim';
	 * 		['country'] = 'Germany';
	 * 		['postal_code'] = '83026';
	 */
	protected function getAddressFromGeo($lat, $lng) {
		$result = array();
		$json = t3lib_div::getUrl('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&region=de&latlng=' . urlencode($lat . ',' . $lng));
		$jsonDecoded = json_decode($json, true);
		if (!empty($jsonDecoded['results'])) {
			foreach ((array) $jsonDecoded['results'][0]['address_components'] as $values) {
				$result[$values['types'][0]] = $values['long_name'];
			}
		}
		return $result;
	}
}

$eid = t3lib_div::makeInstance('eidGetLocation'); // make instance
echo $eid->main(); // print content
?>