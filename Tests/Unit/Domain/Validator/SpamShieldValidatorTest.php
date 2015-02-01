<?php
namespace In2code\Powermail\Tests\Domain\Validator;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Field,
	\In2code\Powermail\Domain\Model\Mail,
	\In2code\Powermail\Domain\Model\Answer;

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
 * SpamShieldValidator Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SpamShieldValidatorTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Domain\Validator\SpamShieldValidator
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Domain\Validator\SpamShieldValidator',
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
	 * Dataprovider calculateMailSpamFactorReturnsVoid()
	 *
	 * @return array
	 */
	public function calculateMailSpamFactorReturnsVoidDataProvider() {
		return array(
			'indication of 0' => array(
				0,
				0.000
			),
			'indication of 1' => array(
				1,
				0.000
			),
			'indication of 2' => array(
				2,
				0.5
			),
			'indication of 5' => array(
				5,
				0.8
			),
			'indication of 8' => array(
				8,
				0.8750
			),
			'indication of 12' => array(
				12,
				0.9167
			),
			'indication of 50' => array(
				50,
				0.9800
			),
			'indication of 50050' => array(
				50050,
				1.000
			),
		);
	}

	/**
	 * Test for calculateMailSpamFactor()
	 *
	 * @param int $spamIndicator
	 * @param float $expectedCalculateMailSpamFactor
	 * @return void
	 * @dataProvider calculateMailSpamFactorReturnsVoidDataProvider
	 * @test
	 */
	public function calculateMailSpamFactorReturnsVoid($spamIndicator, $expectedCalculateMailSpamFactor) {
		$this->generalValidatorMock->_callRef('setSpamIndicator', $spamIndicator);
		$this->generalValidatorMock->_callRef('calculateMailSpamFactor');
		$this->assertSame(
			number_format($expectedCalculateMailSpamFactor, 4),
			number_format($this->generalValidatorMock->_callRef('getCalculatedMailSpamFactor'), 4)
		);
	}

	/**
	 * Dataprovider honeypodCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function honeypodCheckReturnsVoidDataProvider() {
		return array(
			'indication of 1, pot filled' => array(
				1,
				'abc',
				1
			),
			'indication of 3, pot filled' => array(
				3,
				'@test',
				3
			),
			'indication of 2, pot empty' => array(
				2,
				'',
				0
			),
		);
	}

	/**
	 * Test for honeypodCheck()
	 *
	 * @param int $spamIndicator
	 * @param string $pot if $piVars['field']['__hp'] filled
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider honeypodCheckReturnsVoidDataProvider
	 * @test
	 */
	public function honeypodCheckReturnsVoid($spamIndicator, $pot, $expectedOverallSpamIndicator) {
		$this->generalValidatorMock->_set('piVars', array('field' => array('__hp' => $pot)));
		$this->generalValidatorMock->_callRef('honeypodCheck', $spamIndicator);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider linkCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function linkCheckReturnsVoidDataProvider() {
		return array(
			'indication of 1, links allowed 1, 2 links given' => array(
				1,
				1,
				'xx <a href="http://www.test.de">http://www.test.de</a> xx',
				1
			),
			'indication of 7, links allowed 3, 2 links given' => array(
				7,
				3,
				'xx <a href="ftp://www.test.de">https://www.test.de</a> xx',
				0
			),
			'indication of 7, links allowed 0, 1 link given' => array(
				7,
				0,
				'xx <a href="#">https://www.test.de</a> xx',
				7
			),
			'indication of 2, links allowed 2, 3 link given' => array(
				2,
				2,
				'xx [url=http://www.xyz.org]http://www.xyz.org[/url] http://www.xyz.org xx',
				2
			),
		);
	}

	/**
	 * Test for linkCheck()
	 *
	 * @param int $spamIndicator
	 * @param int $allowedLinks
	 * @param string $text
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider linkCheckReturnsVoidDataProvider
	 * @test
	 */
	public function linkCheckReturnsVoid($spamIndicator, $allowedLinks, $text, $expectedOverallSpamIndicator) {
		$mail = new Mail();
		$answer = new Answer();
		$answer->setValue($text);
		$answer->setValueType(0);
		$mail->addAnswer($answer);

		$this->generalValidatorMock->_callRef('linkCheck', $mail, $spamIndicator, $allowedLinks);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider nameCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function nameCheckReturnsVoidDataProvider() {
		return array(
			'indication of 0, same first- and lastname' => array(
				0,
				array(
					'firstname' => 'abcdef',
					'lastname' => 'abcdef',
					'xyz' => '123',
				),
				0
			),
			'indication of 3, same first- and lastname' => array(
				3,
				array(
					'firstname' => 'abcdef',
					'lastname' => 'abcdef',
					'xyz' => '123',
				),
				3
			),
			'indication of 7, different values' => array(
				7,
				array(
					'firstnamex' => 'abcdef',
					'lastname' => 'abcdef',
					'xyz' => '123',
				),
				0
			),
			'indication of 7, same first- and lastname' => array(
				7,
				array(
					'first_name' => 'viagra',
					'surname' => 'viagra',
					'xyz' => '123',
				),
				7
			),
		);
	}

	/**
	 * Test for nameCheck()
	 *
	 * @param int $spamIndicator
	 * @param array $answerProperties
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider nameCheckReturnsVoidDataProvider
	 * @test
	 */
	public function nameCheckReturnsVoid($spamIndicator, $answerProperties, $expectedOverallSpamIndicator) {
		$mail = new Mail();
		foreach ($answerProperties as $fieldName => $value) {
			$field = new Field();
			$field->setMarker($fieldName);
			$answer = new Answer();
			$answer->setField($field);
			$answer->setValue($value);
			$answer->setValueType(132);
			$mail->addAnswer($answer);
		}

		$this->generalValidatorMock->_callRef('nameCheck', $mail, $spamIndicator);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider sessionCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function sessionCheckReturnsVoidDataProvider() {
		return array(
			'indication of 0, time given' => array(
				0,
				1234,
				0
			),
			'indication of 3, time given' => array(
				3,
				1234,
				0
			),
			'indication of 3, no time given' => array(
				3,
				'',
				3
			),
			'indication of 4, no time given' => array(
				4,
				0,
				4
			),
			'indication of 5, no time given' => array(
				5,
				NULL,
				5
			),
		);
	}

	/**
	 * Test for sessionCheck()
	 *
	 * @param int $spamIndicator
	 * @param int $timeFromSession
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider sessionCheckReturnsVoidDataProvider
	 * @test
	 */
	public function sessionCheckReturnsVoid($spamIndicator, $timeFromSession, $expectedOverallSpamIndicator) {
		$this->generalValidatorMock->_callRef('sessionCheck', $spamIndicator, $timeFromSession);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider uniqueCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function uniqueCheckReturnsVoidDataProvider() {
		return array(
			'indication of 0, duplicated values' => array(
				0,
				array(
					'abcdef',
					'abcdef',
					'123',
					'123',
				),
				0
			),
			'indication of 5, duplicated values' => array(
				5,
				array(
					'abcdef',
					'abcdef',
					'123',
					'123',
				),
				5
			),
			'indication of 6, duplicated values' => array(
				5,
				array(
					'alexander',
					'kellner',
					'alexander.kellner@test.org',
					'This is an example text',
					array(
						'abc',
						'def'
					)
				),
				0
			),
		);
	}

	/**
	 * Test for uniqueCheck()
	 *
	 * @param int $spamIndicator
	 * @param array $answerProperties
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider uniqueCheckReturnsVoidDataProvider
	 * @test
	 */
	public function uniqueCheckReturnsVoid($spamIndicator, $answerProperties, $expectedOverallSpamIndicator) {
		$mail = new Mail();
		foreach ($answerProperties as $value) {
			$answer = new Answer();
			$answer->setValue($value);
			$answer->setValueType(123);
			$mail->addAnswer($answer);
		}

		$this->generalValidatorMock->_callRef('uniqueCheck', $mail, $spamIndicator);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider blacklistStringCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function blacklistStringCheckReturnsVoidDataProvider() {
		return array(
			'indication of 0, blacklisted values' => array(
				0,
				array(
					'abcdef',
					'abcdef',
					'123',
					'123',
				),
				'abcdef,123,xyz',
				0
			),
			'indication of 3, blacklisted values' => array(
				3,
				array(
					'abcdef',
					'abcdef',
					'123',
					'123',
				),
				'abcdef,123,xyz',
				3
			),
			'indication of 7, blacklisted values' => array(
				7,
				array(
					'buy cheap v!agra now',
					'all is fine',
				),
				'viagra   ,  v!agra  , v1agra',
				7
			),
		);
	}

	/**
	 * Test for blacklistStringCheck()
	 *
	 * @param int $spamIndicator
	 * @param array $answerProperties
	 * @param string $blacklist
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider blacklistStringCheckReturnsVoidDataProvider
	 * @test
	 */
	public function blacklistStringCheckReturnsVoid($spamIndicator, $answerProperties, $blacklist, $expectedOverallSpamIndicator) {
		$mail = new Mail();
		foreach ($answerProperties as $value) {
			$answer = new Answer();
			$answer->setValue($value);
			$answer->setValueType(123);
			$mail->addAnswer($answer);
		}
		$this->generalValidatorMock->_set(
			'settings',
			array(
				'spamshield.' => array(
					'indicator.' => array(
						'blacklistStringValues' => $blacklist
					)
				)
			)
		);
		$this->generalValidatorMock->_callRef('blacklistStringCheck', $mail, $spamIndicator);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider blacklistIpCheckReturnsVoid()
	 *
	 * @return array
	 */
	public function blacklistIpCheckReturnsVoidDataProvider() {
		return array(
			'indication of 0, blacklisted ip' => array(
				0,
				'123.123.123.123',
				'123.123.123.123',
				0
			),
			'indication of 3, blacklisted ip' => array(
				3,
				'123.123.123.123',
				'123.123.123.123',
				3
			),
			'indication of 4, blacklisted ip' => array(
				4,
				',23.123.123.12,',
				'23.123.123.12',
				4
			),
			'indication of 5, blacklisted ip' => array(
				4,
				'192.168.0.2 , 		23.166.12.12 , 250.182.0.3',
				'23.166.12.12',
				4
			),
			'indication of 6, no blacklisted ip' => array(
				4,
				'192.168.0.2 , 		23.166.12.12 , 250.182.0.3',
				'23.166.12.1',
				0
			),
		);
	}

	/**
	 * Test for blacklistIpCheck()
	 *
	 * @param int $spamIndicator
	 * @param string $blacklist
	 * @param string $userIp
	 * @param int $expectedOverallSpamIndicator
	 * @return void
	 * @dataProvider blacklistIpCheckReturnsVoidDataProvider
	 * @test
	 */
	public function blacklistIpCheckReturnsVoid($spamIndicator, $blacklist, $userIp, $expectedOverallSpamIndicator) {
		$this->generalValidatorMock->_set(
			'settings',
			array(
				'spamshield.' => array(
					'indicator.' => array(
						'blacklistIpValues' => $blacklist
					)
				)
			)
		);
		$this->generalValidatorMock->_callRef('blacklistIpCheck', $spamIndicator, $userIp);
		$this->assertSame(
			$expectedOverallSpamIndicator,
			$this->generalValidatorMock->_callRef('getSpamIndicator')
		);
	}

	/**
	 * Dataprovider formatSpamFactorReturnsString()
	 *
	 * @return array
	 */
	public function formatSpamFactorReturnsStringDataProvider() {
		return array(
			array(
				0.23,
				'23%',
			),
			array(
				0.0,
				'0%',
			),
			array(
				1.0,
				'100%',
			),
			array(
				0.999999999,
				'100%',
			),
		);
	}

	/**
	 * Test for formatSpamFactor()
	 *
	 * @param float $factor
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider formatSpamFactorReturnsStringDataProvider
	 * @test
	 */
	public function formatSpamFactorReturnsString($factor, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			$this->generalValidatorMock->_callRef('formatSpamFactor', $factor)
		);
	}

	/**
	 * Dataprovider isSpamToleranceLimitReachedReturnsBool()
	 *
	 * @return array
	 */
	public function isSpamToleranceLimitReachedReturnsBoolDataProvider() {
		return array(
			array(
				0.8,
				0.9,
				FALSE
			),
			array(
				0.5312,
				0.54,
				FALSE
			),
			array(
				0.9,
				0.8,
				TRUE
			),
			array(
				0.0,
				0.0,
				TRUE
			),
			array(
				0.01,
				0.0,
				TRUE
			),
			array(
				1.0,
				1.0,
				TRUE
			),
		);
	}

	/**
	 * Test for isSpamToleranceLimitReached()
	 *
	 * @param float $calculatedMailSpamFactor
	 * @param float $spamFactorLimit
	 * @param bool $expectedResult
	 * @return void
	 * @dataProvider isSpamToleranceLimitReachedReturnsBoolDataProvider
	 * @test
	 */
	public function isSpamToleranceLimitReachedReturnsBool($calculatedMailSpamFactor, $spamFactorLimit, $expectedResult) {
		$this->generalValidatorMock->_set('calculatedMailSpamFactor', $calculatedMailSpamFactor);
		$this->generalValidatorMock->_set('spamFactorLimit', $spamFactorLimit);
		$this->assertSame(
			$expectedResult,
			$this->generalValidatorMock->_callRef('isSpamToleranceLimitReached')
		);
	}
}