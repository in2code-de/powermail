<?php
namespace In2code\Powermail\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Get countries from static_info_tables
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class CountriesFromStaticInfoTables {

	/**
	 * countryRepository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
	 * @inject
	 */
	protected $countryRepository;

	/**
	 * Build an country array
	 *
	 * @param \string $key
	 * @param \string $value
	 * @param \string $sortbyField
	 * @param \string $sorting
	 * @return \array
	 */
	public function getCountries($key = 'isoCodeA3', $value = 'officialNameLocal', $sortbyField = 'isoCodeA3', $sorting = 'asc') {
		$countries = $this->countryRepository->findAllOrderedBy($sortbyField, $sorting);
		$countriesArray = array();
		foreach ($countries as $country) {
			if (
				method_exists($country, 'get' . ucfirst($key)) &&
				method_exists($country, 'get' . ucfirst($value))
			) {
				$countriesArray[$country->{'get' . ucfirst($key)}()] = $country->{'get' . ucfirst($value)}();
			} else {
				$countriesArray[$country->getIsoCodeA3()] = $country->getOfficialNameLocal();
			}
		}
		return $countriesArray;
	}
}