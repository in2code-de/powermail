<?php
namespace In2code\Powermail\Tests\Domain\Validator;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Mail,
	\In2code\Powermail\Domain\Model\Field,
	\In2code\Powermail\Domain\Model\Answer,
	\TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * InputValidator Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class InputValidatorTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Domain\Validator\InputValidator
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Domain\Validator\InputValidator',
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
	 * Dataprovider getAnswerFromFieldReturnsString()
	 *
	 * @return array
	 */
	public function getAnswerFromFieldReturnsStringDataProvider() {
		return array(
			array(
				array(
					123 => 'value abc',
					124 => 'value def',
					125 => 'value ghi'
				),
				'value ghi'
			),
			array(
				array(
					1 => 'firstname',
					3 => 'lastname',
					9 => 'email@email.org'
				),
				'email@email.org'
			),
		);
	}

	/**
	 * Test for getAnswerFromField()
	 *
	 * @param array $fieldAnswerMix
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getAnswerFromFieldReturnsStringDataProvider
	 * @test
	 */
	public function getAnswerFromFieldReturnsString($fieldAnswerMix, $expectedResult) {
		$mail = new Mail();
		foreach ($fieldAnswerMix as $fieldUid => $answerValue) {
			$answer = new Answer();
			$answer->setValue($answerValue);
			$field = new Field();
			$field->_setProperty('uid', $fieldUid);
			$answer->setField($field);
			$mail->addAnswer($answer);
		}

		$this->assertSame(
			$expectedResult,
			$this->generalValidatorMock->_callRef('getAnswerFromField', $field, $mail)
		);
	}
}