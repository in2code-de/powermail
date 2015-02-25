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
 * UploadValidator Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class UploadValidatorTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Domain\Validator\UploadValidator
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {

		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Domain\Validator\UploadValidator',
			array('setErrorAndMessage')
		);
		$settings = array(
			'misc.' => array(
				'file.' => array(
					'extension' =>
						'jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,flv,mpg,mpeg,avi,mp3,zip,rar,ace,csv'
				)
			)
		);
		$this->generalValidatorMock->_set('settings', $settings);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Dataprovider validateIsValidReturnsBool()
	 *
	 * @return array
	 */
	public function validateIsValidReturnsBoolDataProvider() {
		return array(
			array(
				'test.jpg',
				TRUE
			),
			array(
				'test.jpg.php',
				FALSE
			),
			array(
				'test.php.123',
				FALSE
			),
			array(
				'test.png',
				TRUE
			),
			array(
				'fileadmin/folder/this.is.a.pdf',
				TRUE
			),
			array(
				'fileadmin/folder/this.is.a.htaccess',
				FALSE
			),
			array(
				'.htaccess',
				FALSE
			),
			array(
				'test.123',
				FALSE
			),
		);
	}

	/**
	 * Test for isValid()
	 *
	 * @param \string $value
	 * @param \bool $expectedResult
	 * @return void
	 * @dataProvider validateIsValidReturnsBoolDataProvider
	 * @test
	 */
	public function validateIsValidReturnsBool($value, $expectedResult) {
		$mail = new Mail;
		$field = new Field;
		$field->setType(1);
		$answer1 = new Answer;
		$answer1->setValueType(3);
		$answer1->setValue($value);
		$answer1->setField($field);
		$objectStorage = new ObjectStorage;
		$objectStorage->attach($answer1);
		$mail->setAnswers($objectStorage);

		if ($expectedResult === FALSE) {
			$this->generalValidatorMock->expects($this->once())->method('setErrorAndMessage');
		} else {
			$this->generalValidatorMock->expects($this->never())->method('setErrorAndMessage');
		}
		$this->generalValidatorMock->_callRef('isValid', $mail);
	}
}