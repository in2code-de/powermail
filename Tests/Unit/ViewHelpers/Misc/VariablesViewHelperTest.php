<?php
namespace In2code\Powermail\Tests\ViewHelpers\Misc;

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
 * VariablesViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class VariablesViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Misc\VariablesViewHelper',
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
	 * Dataprovider for removePowermailAllParagraphTagWrapReturnsString()
	 *
	 * @return array
	 */
	public function removePowermailAllParagraphTagWrapReturnsStringDataProvider() {
		return array(
			array(
				'<p class="abc">xyz</p><p>{powermail_all}</p><p class="abc">xyz</p>',
				'<p class="abc">xyz</p>{powermail_all}<p class="abc">xyz</p>'
			),
			array(
				'<p>{powermail_all}</p>',
				'{powermail_all}'
			),
			array(
				'<b>{powermail_all}</b>',
				'<b>{powermail_all}</b>'
			),
			array(
				'<p> {powermail_all} </p>',
				'{powermail_all}'
			),
			array(
				'{powermail_all}',
				'{powermail_all}'
			),
			array(
				'<p class="abc">xyz</p><p>{powermail_all}</p>',
				'<p class="abc">xyz</p>{powermail_all}'
			),
			array(
				'<p>{powermail_all}</p><p class="abc">xyz</p>',
				'{powermail_all}<p class="abc">xyz</p>'
			),
			array(
				'<table><tr><td>{powermail_all}</td></tr></table>',
				'<table><tr><td>{powermail_all}</td></tr></table>'
			),
			array(
				'<table><tr><td><p>	{powermail_all} </p></td></tr></table>',
				'<table><tr><td>{powermail_all}</td></tr></table>'
			),
		);
	}

	/**
	 * Test for removePowermailAllParagraphTagWrap()
	 *
	 * @param string $content
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider removePowermailAllParagraphTagWrapReturnsStringDataProvider
	 * @test
	 */
	public function removePowermailAllParagraphTagWrapReturnsString($content, $expectedResult) {
		$result = $this->abstractValidationViewHelperMock->_callRef('removePowermailAllParagraphTagWrap', $content);
		$this->assertSame($expectedResult, $result);
	}
}