<?php
namespace In2code\Powermail\Tests\ViewHelpers\BeCheck;

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
 * PowermailVersionNoteViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class PowermailVersionNoteViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\BeCheck\PowermailVersionNoteViewHelper',
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
	 * Dataprovider for renderReturnsInt()
	 *
	 * @return array
	 */
	public function renderReturnsIntDataProvider() {
		return array(
			array(
				FALSE,
				FALSE,
				FALSE,
				FALSE,
				0
			),
			array(
				TRUE,
				TRUE,
				TRUE,
				FALSE,
				3
			),
			array(
				FALSE,
				TRUE,
				TRUE,
				TRUE,
				0
			),
			array(
				TRUE,
				FALSE,
				TRUE,
				FALSE,
				1
			),
			array(
				TRUE,
				TRUE,
				TRUE,
				TRUE,
				2
			),
			array(
				TRUE,
				FALSE,
				TRUE,
				TRUE,
				2
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param bool $extensionTableExists
	 * @param bool $isNewerVersionAvailable
	 * @param bool $currentVersionInExtensionTableExists
	 * @param bool $isCurrentVersionUnsecure
	 * @param int $expectedResult
	 * @return void
	 * @dataProvider renderReturnsIntDataProvider
	 * @test
	 */
	public function renderReturnsInt(
		$extensionTableExists,
		$isNewerVersionAvailable,
		$currentVersionInExtensionTableExists,
		$isCurrentVersionUnsecure,
		$expectedResult
	) {
		$this->abstractValidationViewHelperMock->_set('checkFromDatabase', FALSE);
		$this->abstractValidationViewHelperMock->_callRef('setExtensionTableExists', $extensionTableExists);
		$this->abstractValidationViewHelperMock->_callRef('setIsNewerVersionAvailable', $isNewerVersionAvailable);
		$this->abstractValidationViewHelperMock->_callRef(
			'setCurrentVersionInExtensionTableExists',
			$currentVersionInExtensionTableExists
		);
		$this->abstractValidationViewHelperMock->_callRef('setIsCurrentVersionUnsecure', $isCurrentVersionUnsecure);
		$result = $this->abstractValidationViewHelperMock->_callRef('render');
		$this->assertSame($expectedResult, $result);
	}
}