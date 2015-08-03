<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * BasicFileUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class BasicFileUtiltyTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\BasicFileUtility
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\BasicFileUtility',
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
	 * @var string
	 */
	protected $defaultFileExtensions =
		'jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,flv,mpg,mpeg,avi,mp3,zip,rar,ace,csv';

	/**
	 * Data Provider for cleanFileNameReturnBool()
	 *
	 * @return array
	 */
	public function cleanFileNameReturnBoolDataProvider() {
		return array(
			array(
				'test.jpg',
				'_',
				'test.jpg'
			),
			array(
				'image.png.pdf',
				'_',
				'image.png.pdf'
			),
			array(
				'image-01.pdf',
				'_',
				'image-01.pdf'
			),
			array(
				'image_01.pdf',
				'_',
				'image_01_.pdf'
			),
			array(
				'picture.PNG',
				'_',
				'picture.png'
			),
			array(
				'super Fälnäm+.abc.jpg',
				'_',
				'super_f__ln__m_.abc.jpg'
			),
			array(
				'ßUPER HIT.BMP',
				'-',
				'--uper-hit.bmp'
			),
		);
	}

	/**
	 * cleanFileNameReturnBool Test
	 *
	 * @param string $filename
	 * @param string $replace
	 * @param string $expectedFilename
	 * @dataProvider cleanFileNameReturnBoolDataProvider
	 * @return void
	 * @test
	 */
	public function cleanFileNameReturnBool($filename, $replace, $expectedFilename) {
		BasicFileUtility::cleanFileName($filename, $replace);
		$this->assertSame($expectedFilename, $filename);
	}

	/**
	 * Data Provider for removeAppendingNumbersInStringReturnString()
	 *
	 * @return array
	 */
	public function removeAppendingNumbersInStringReturnStringDataProvider() {
		return array(
			array(
				'test_01',
				'test'
			),
			array(
				'test_01_01',
				'test_01'
			),
			array(
				'test',
				'test'
			),
			array(
				'test_',
				'test_'
			),
			array(
				'01_82',
				'01'
			),
			array(
				'test_02_03_04',
				'test_02_03'
			),
			array(
				'test_123456',
				'test'
			),
			array(
				'test_651_98746854',
				'test_651'
			),
		);
	}

	/**
	 * removeAppendingNumbersInString Test
	 *
	 * @param string $string
	 * @param string $expectedString
	 * @dataProvider removeAppendingNumbersInStringReturnStringDataProvider
	 * @return void
	 * @test
	 */
	public function removeAppendingNumbersInStringReturnString($string, $expectedString) {
		$string = $this->generalValidatorMock->_call('removeAppendingNumbersInString', $string);
		$this->assertSame($expectedString, $string);
	}

	/**
	 * Data Provider for dontAllowAppendingNumbersInFileNameReturnString()
	 *
	 * @return array
	 */
	public function dontAllowAppendingNumbersInFileNameReturnStringDataProvider() {
		return array(
			array(
				'test.jpg',
				'test.jpg'
			),
			array(
				'test_01.jpg',
				'test_01_.jpg'
			),
			array(
				'test_685468.jpg',
				'test_685468_.jpg'
			),
			array(
				'123.jpg',
				'123.jpg'
			),
			array(
				'abc_.jpg',
				'abc_.jpg'
			),
			array(
				'abc123.jpg',
				'abc123.jpg'
			),
		);
	}

	/**
	 * dontAllowAppendingNumbersInFileName Test
	 *
	 * @param string $string
	 * @param string $expectedString
	 * @dataProvider dontAllowAppendingNumbersInFileNameReturnStringDataProvider
	 * @return void
	 * @test
	 */
	public function dontAllowAppendingNumbersInFileNameReturnString($string, $expectedString) {
		$string = $this->generalValidatorMock->_call('dontAllowAppendingNumbersInFileName', $string);
		$this->assertSame($expectedString, $string);
	}

	/**
	 * Data Provider for checkExtensionReturnBool()
	 *
	 * @return array
	 */
	public function checkExtensionReturnBoolDataProvider() {
		return array(
			array(
				'test.jpg',
				$this->defaultFileExtensions,
				TRUE
			),
			array(
				'test_02.txt',
				$this->defaultFileExtensions,
				TRUE
			),
			array(
				'test.php.jpg',
				$this->defaultFileExtensions,
				FALSE
			),
			array(
				'test.jpg.php',
				$this->defaultFileExtensions,
				FALSE
			),
			array(
				'test.jpg',
				'jpeg,png,wav',
				FALSE
			),
			array(
				'../test.jpg',
				$this->defaultFileExtensions,
				FALSE
			),
			array(
				'folder/../test.jpg',
				$this->defaultFileExtensions,
				FALSE
			),
			array(
				'test.jpg',
				'',
				FALSE
			),
			array(
				'.htaccess',
				'',
				FALSE
			),
			array(
				'.htaccess',
				$this->defaultFileExtensions,
				FALSE
			),
			array(
				'.htaccess',
				$this->defaultFileExtensions,
				FALSE
			),
		);
	}

	/**
	 * checkExtensionReturnBool Test
	 *
	 * @param string $filename
	 * @param string $allowedFileExtensions
	 * @dataProvider checkExtensionReturnBoolDataProvider
	 * @return void
	 * @test
	 */
	public function checkExtensionReturnBool($filename, $allowedFileExtensions, $expectedResult) {
		$result = BasicFileUtility::checkExtension($filename, $allowedFileExtensions);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * hasFormAnUploadFieldReturnBool Test
	 *
	 * @return void
	 * @test
	 */
	public function hasFormAnUploadFieldReturnBool() {
		$fieldObjectStorage = new ObjectStorage;
		$field = new Field;
		$field->setType('captcha');
		$fieldObjectStorage->attach($field);
		$field2 = new Field;
		$field2->setType('file');
		$fieldObjectStorage->attach($field2);
		$pagesObjectStorage = new ObjectStorage;
		$page = new Page;
		$page->setFields($fieldObjectStorage);
		$pagesObjectStorage->attach($page);
		$form = new Form;
		$form->setPages($pagesObjectStorage);
		$this->assertTrue(BasicFileUtility::hasFormAnUploadField($form));

		$field2->setType('textarea');
		$this->assertFalse(BasicFileUtility::hasFormAnUploadField($form));
	}
}