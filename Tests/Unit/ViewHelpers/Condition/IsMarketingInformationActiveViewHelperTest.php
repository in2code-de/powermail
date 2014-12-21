<?php
namespace In2code\Powermail\Tests\ViewHelpers\Condition;

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
 * IsMarketingInformationActiveViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class IsMarketingInformationActiveViewHelperTest extends UnitTestCase {

	/**
	 * @var array
	 */
	protected $marketingInformation = array(
		'refererDomain' => 'test.org',
		'referer' => '',
		'country' => '',
		'mobileDevice' => 0,
		'frontendLanguage' => 0,
		'browserLanguage' => '',
		'pageFunnel' => array()
	);

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $isNotExcludedFromPowermailAllViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->isNotExcludedFromPowermailAllViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Condition\IsMarketingInformationActiveViewHelper',
			array('dummy')
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->isNotExcludedFromPowermailAllViewHelperMock);
	}

	/**
	 * Dataprovider for renderReturnsBool()
	 *
	 * @return array
	 */
	public function renderReturnsBoolDataProvider() {
		return array(
			array(
				array(
					'marketing' => array(
						'information' => '0'
					),
					'global' => array(
						'disableMarketingInformation' => '1'
					)
				),
				$this->marketingInformation,
				FALSE
			),
			array(
				array(
					'marketing' => array(
						'information' => '1'
					),
					'global' => array(
						'disableMarketingInformation' => '0'
					)
				),
				$this->marketingInformation,
				TRUE
			),
			array(
				array(
					'marketing' => array(
						'information' => '1'
					),
					'global' => array(
						'disableMarketingInformation' => '0'
					)
				),
				array(),
				FALSE
			),
			array(
				array(
					'marketing' => array(
						'information' => '0'
					),
					'global' => array(
						'disableMarketingInformation' => '0'
					)
				),
				$this->marketingInformation,
				FALSE
			),
			array(
				array(
					'marketing' => array(
						'information' => '1'
					),
					'global' => array(
						'disableMarketingInformation' => '1'
					)
				),
				$this->marketingInformation,
				FALSE
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param array $settings
	 * @param array $marketingInformation
	 * @param bool $expectedResult
	 * @return void
	 * @dataProvider renderReturnsBoolDataProvider
	 * @test
	 */
	public function renderReturnsBool($settings, $marketingInformation, $expectedResult) {
		$result = $this->isNotExcludedFromPowermailAllViewHelperMock->_callRef(
			'render',
			$marketingInformation,
			$settings
		);
		$this->assertSame($expectedResult, $result);
	}
}