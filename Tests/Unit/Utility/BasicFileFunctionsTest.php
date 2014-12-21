<?php
namespace In2code\Powermail\Tests\Utility;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Utility\BasicFileFunctions,
	\In2code\Powermail\Domain\Model\Field,
	\In2code\Powermail\Domain\Model\Page,
	\In2code\Powermail\Domain\Model\Form,
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
 * BasicFileFunctions Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class BasicFileFunctionsTest extends UnitTestCase {

	/**
	 * @var string
	 */
	protected $defaultFileExtensions =
		'jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,flv,mpg,mpeg,avi,mp3,zip,rar,ace,csv';

	/**
	 * @return void
	 */
	public function setUp() {
	}

	/**
	 * @return void
	 */
	public function tearDown() {
	}

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
				'image_01.pdf'
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
		BasicFileFunctions::cleanFileName($filename, $replace);
		$this->assertSame($expectedFilename, $filename);
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
		$result = BasicFileFunctions::checkExtension($filename, $allowedFileExtensions);
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
		$this->assertTrue(BasicFileFunctions::hasFormAnUploadField($form));

		$field2->setType('textarea');
		$this->assertFalse(BasicFileFunctions::hasFormAnUploadField($form));
	}
}