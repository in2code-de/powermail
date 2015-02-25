<?php
namespace In2code\Powermail\Tests\Utility;

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
 * CalculatingCaptcha Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class CalculatingCaptchaTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\CalculatingCaptcha
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\CalculatingCaptcha',
			array('dummy')
		);
		$this->generalValidatorMock->_set(
			'configuration',
			array(
				'captcha.' => array(
					'default.' => array(
						'image' => 'EXT:powermail/Resources/Private/Image/captcha_bg.png',
						'font' => 'EXT:powermail/Resources/Private/Fonts/ARCADE.TTF'
					)
				)
			)
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Data Provider for createImageReturnString()
	 *
	 * @return array
	 */
	public function createImageReturnStringDataProvider() {
		return array(
			array(
				'1+3',
				'typo3temp/tx_powermail/CalculatingCaptcha.png'
			),
			array(
				'5 + 1',
				'typo3temp/tx_powermail/CalculatingCaptcha.png'
			)
		);
	}

	/**
	 * cleanFileNameReturnBool Test
	 *
	 * @param string $content
	 * @param string $expectedResult
	 * @dataProvider createImageReturnStringDataProvider
	 * @return void
	 * @test
	 */
	public function createImageReturnString($content, $expectedResult) {
		$result = $this->generalValidatorMock->_call('createImage', $content, FALSE);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Data Provider for getStringForCaptchaReturnsArray()
	 *
	 * @return array
	 */
	public function getStringForCaptchaReturnsArrayDataProvider() {
		return array(
			array(
				'1+3',
				array(
					4,
					'1 + 3'
				)
			),
			array(
				'88 + 11',
				array(
					99,
					'88 + 11'
				)
			),
			array(
				'12 - 8',
				array(
					4,
					'12 - 8'
				)
			),
			array(
				'6:3',
				array(
					2,
					'6 : 3'
				)
			),
			array(
				'33x3',
				array(
					99,
					'33 x 3'
				)
			),
		);
	}

	/**
	 * getStringForCaptcha Test
	 *
	 * @param string $forceValue
	 * @param string $expectedResult
	 * @dataProvider getStringForCaptchaReturnsArrayDataProvider
	 * @return void
	 * @test
	 */
	public function getStringForCaptchaReturnsArray($forceValue, $expectedResult) {
		$this->generalValidatorMock->_set(
			'configuration',
			array(
				'captcha.' => array(
					'default.' => array(
						'forceValue' => $forceValue
					)
				)
			)
		);
		$result = $this->generalValidatorMock->_call('getStringForCaptcha');
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Data Provider for mathematicOperationReturnsInt()
	 *
	 * @return array
	 */
	public function mathematicOperationReturnsIntDataProvider() {
		return array(
			array(
				1,
				3,
				'+',
				4
			),
			array(
				7,
				2,
				'-',
				5
			),
			array(
				6,
				3,
				':',
				2
			),
			array(
				11,
				3,
				'x',
				33
			),
		);
	}

	/**
	 * getStringForCaptcha Test
	 *
	 * @param int $number1
	 * @param int $number2
	 * @param string $operator
	 * @param string $expectedResult
	 * @dataProvider mathematicOperationReturnsIntDataProvider
	 * @return void
	 * @test
	 */
	public function mathematicOperationReturnsInt($number1, $number2, $operator, $expectedResult) {
		$result = $this->generalValidatorMock->_call('mathematicOperation', $number1, $number2, $operator);
		$this->assertSame($expectedResult, $result);
	}
}