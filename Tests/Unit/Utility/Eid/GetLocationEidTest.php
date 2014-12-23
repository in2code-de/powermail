<?php
namespace In2code\Powermail\Tests\Utility\Eid;

use \TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * BasicFileFunctions Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class GetLocationEidTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Eid\GetLocationEid
	 */
	protected $getLocationEidMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->getLocationEidMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\Eid\GetLocationEid',
			array('dummy')
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->getLocationEidMock);
	}

	/**
	 * Dataprovider getAddressFromGeoReturnsArray()
	 *
	 * @return array
	 */
	public function getAddressFromGeoReturnsArrayDataProvider() {
		return array(
			'in2code GmbH, Rosenheim, Germany' => array(
				47.84787,
				12.113768,
				array(
					'route' => 'Kunstmühlstraße',
					'locality' => 'Rosenheim',
					'country' => 'Germany',
					'postal_code' => '83026'
				)
			),
			'Eisweiherweg, Forsting, Germany' => array(
				48.0796126,
				12.0898908,
				array(
					'route' => 'Eisweiherweg',
					'locality' => 'Pfaffing',
					'country' => 'Germany',
					'postal_code' => '83539'
				)
			),
			'Baker Street, London, UK' => array(
				51.5205573,
				-0.1566651,
				array(
					'route' => 'Baker Street',
					'locality' => 'London',
					'country' => 'United Kingdom',
					'postal_code' => 'W1U 6TJ'
				)
			),
		);
	}

	/**
	 * Test for getValue()
	 *
	 * @param float $latitude
	 * @param float $longitude
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider getAddressFromGeoReturnsArrayDataProvider
	 * @test
	 */
	public function getAddressFromGeoReturnsArray($latitude, $longitude, $expectedResult) {
		$address = $this->getLocationEidMock->_callRef('getAddressFromGeo', $latitude, $longitude);
		foreach (array_keys($expectedResult) as $expectedResultSingleKey) {
			$this->assertSame($expectedResult[$expectedResultSingleKey], $address[$expectedResultSingleKey]);
		}
	}
}