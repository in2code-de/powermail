<?php
namespace In2code\Powermail\Tests\Controller;

use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Request;
use In2code\Powermail\Domain\Model\Mail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * FormController Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FormControllerTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Controller\FormController
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Controller\FormController',
			array('dummy')
		);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Dataprovider forwardIfFormParamsDoNotMatchReturnsVoid()
	 *
	 * @return array
	 */
	public function forwardIfFormParamsDoNotMatchReturnsVoidDataProvider() {
		return array(
			'not allowed form given, forward' => array(
				array(
					'mail' => array(
						'form' => '1'
					)
				),
				array(
					'main' => array(
						'form' => '2,3'
					)
				),
				TRUE
			),
			'allowed form given, do not forward' => array(
				array(
					'mail' => array(
						'form' => '1'
					)
				),
				array(
					'main' => array(
						'form' => '1,2,3'
					)
				),
				FALSE
			),
			'mail object given, do not forward' => array(
				array(
					'mail' => new Mail()
				),
				array(
					'main' => array(
						'form' => '2,3'
					)
				),
				FALSE
			),
			'nothing given, do not forward' => array(
				array(),
				array(
					'main' => array(
						'form' => '2,3'
					)
				),
				FALSE
			),
		);
	}

	/**
	 * Test for forwardIfFormParamsDoNotMatch()
	 *
	 * @param array $arguments
	 * @param array $settings
	 * @param bool $forwardActive
	 * @return void
	 * @dataProvider forwardIfFormParamsDoNotMatchReturnsVoidDataProvider
	 * @test
	 */
	public function forwardIfFormParamsDoNotMatchReturnsVoid($arguments, $settings, $forwardActive) {
		$request = new Request();
		$request->setArguments($arguments);
		$this->generalValidatorMock->_set('request', $request);
		$this->generalValidatorMock->_set('settings', $settings);
		try {
			// if forward() is called, an exception will be thrown
			$this->generalValidatorMock->_callRef('forwardIfFormParamsDoNotMatch');
		} catch (\Exception $exception) {
			return;
		}
		$this->assertFalse($forwardActive);
	}

	/**
	 * Dataprovider isMailPersistActiveReturnBool()
	 *
	 * @return array
	 */
	public function isMailPersistActiveReturnBoolDataProvider() {
		return array(
			'store 0, optin 0, hash NULL' => array(
				'0',
				'0',
				NULL,
				FALSE
			),
			'store 0, optin 0, hash NOTNULL' => array(
				'0',
				'0',
				'abc',
				FALSE
			),
			'store 0, optin 1, hash NULL' => array(
				'0',
				'1',
				NULL,
				TRUE
			),
			'store 0, optin 1, hash NOTNULL' => array(
				'0',
				'1',
				'abc',
				FALSE
			),
			'store 1, optin 0, hash NULL' => array(
				'1',
				'0',
				NULL,
				TRUE
			),
			'store 1, optin 0, hash NOTNULL' => array(
				'1',
				'0',
				'abc',
				FALSE
			),
			'store 1, optin 1, hash NULL' => array(
				'1',
				'1',
				NULL,
				TRUE
			),
			'store 1, optin 1, hash NOTNULL' => array(
				'1',
				'1',
				'abc',
				FALSE
			)
		);
	}

	/**
	 * Test for isMailPersistActive()
	 *
	 * @param int $store
	 * @param int $optin
	 * @param NULL|string $hash
	 * @param bool $expectedResult
	 * @return void
	 * @dataProvider isMailPersistActiveReturnBoolDataProvider
	 * @test
	 */
	public function isMailPersistActiveReturnBool($store, $optin, $hash, $expectedResult) {
		$settings = array(
			'db' => array(
				'enable' => $store
			),
			'main' => array(
				'optin' => $optin
			)
		);
		$this->generalValidatorMock->_set('settings', $settings);
		$this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('isMailPersistActive', $hash));
	}
}