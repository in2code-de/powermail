<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * FrontendUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FrontendUtilityTest extends UnitTestCase {

	/**
	 * Test for getStoragePage()
	 *
	 * @return void
	 * @test
	 */
	public function getStoragePageReturnsInt() {
		$result = FrontendUtility::getStoragePage(123);
		$this->assertSame(123, $result);
	}

	/**
	 * Data Provider for getDomainFromUriReturnsString()
	 *
	 * @return array
	 */
	public function getDomainFromUriReturnsStringDataProvider() {
		return array(
			array(
				'http://subdomain.domain.org/folder/file.html',
				'subdomain.domain.org'
			),
			array(
				'ftp://domain.org',
				'domain.org'
			),
			array(
				'https://www.domain.co.uk/',
				'www.domain.co.uk'
			),
		);
	}

	/**
	 * getDomainFromUri Test
	 *
	 * @param string $value
	 * @param string $expectedResult
	 * @dataProvider getDomainFromUriReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getDomainFromUriReturnsString($value, $expectedResult) {
		$this->assertSame($expectedResult, FrontendUtility::getDomainFromUri($value));
	}

	/**
	 * Data Provider for getCountryFromIpReturnsString()
	 *
	 * @return array
	 */
	public function getCountryFromIpReturnsStringDataProvider() {
		return array(
			array(
				'217.72.208.133',
				'Germany'
			),
			array(
				'27.121.255.4',
				'Japan'
			),
			array(
				'5.226.31.255',
				'Spain'
			),
			array(
				'66.85.131.18',
				'United States'
			),
			array(
				'182.118.23.7',
				'China'
			),
		);
	}

	/**
	 * getCountryFromIp Test
	 *
	 * @param string $ip
	 * @param string $expectedResult
	 * @dataProvider getCountryFromIpReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getCountryFromIpReturnsString($ip, $expectedResult) {
		$this->assertSame($expectedResult, FrontendUtility::getCountryFromIp($ip));
	}

	/**
	 * Dataprovider getSubFolderOfCurrentUrlReturnsString()
	 *
	 * @return array
	 */
	public function getSubFolderOfCurrentUrlReturnsStringDataProvider() {
		return array(
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				''
			),
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder'
			),
		);
	}

	/**
	 * Test for getSubFolderOfCurrentUrl()
	 *
	 * @param bool $leadingSlash will be prepended
	 * @param bool $trailingSlash will be appended
	 * @param string $host
	 * @param string $url
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getSubFolderOfCurrentUrlReturnsStringDataProvider
	 * @test
	 */
	public function getSubFolderOfCurrentUrlReturnsString($leadingSlash, $trailingSlash, $host, $url, $expectedResult) {
		$result = FrontendUtility::getSubFolderOfCurrentUrl($leadingSlash, $trailingSlash, $host, $url);
		$this->assertSame($expectedResult, $result);
	}
}