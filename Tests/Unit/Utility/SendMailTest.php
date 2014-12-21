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
 * SendMail Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SendMailTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\SendMail
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\SendMail',
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
	 * Data Provider for br2nlReturnString()
	 *
	 * @return array
	 */
	public function br2nlReturnStringDataProvider() {
		return array(
			array(
				'a<br>b',
				"a\nb"
			),
			array(
				'a<br><br /><br/>b',
				"a\n\n\nb"
			),
			array(
				'a\nbr[br]b',
				'a\nbr[br]b'
			),
		);
	}

	/**
	 * cleanFileNameReturnBool Test
	 *
	 * @param string $content
	 * @param string $expectedResult
	 * @dataProvider br2nlReturnStringDataProvider
	 * @return void
	 * @test
	 */
	public function br2nlReturnString($content, $expectedResult) {
		$result = $this->generalValidatorMock->_call('br2nl', $content);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Data Provider for makePlainReturnString()
	 *
	 * @return array
	 */
	public function makePlainReturnStringDataProvider() {
		return array(
			array(
				'a<br>b',
				"a\nb"
			),
			array(
				'<p>test</p><p>test</p>',
				"test\ntest"
			),
			array(
				"<table>\n\t\n<tr><th>a</th><th>b</th></tr><td>\nc</td><td>d</td></table>",
				"a b \nc d"
			),
			array(
				'<h1>t</h1><p>p</p><br>x',
				"t\np\n\nx"
			),
			array(
				'a<ul><li>b</li><li>c</li></ul>d',
				"a\nb\nc\nd"
			),
		);
	}

	/**
	 * cleanFileNameReturnBool Test
	 *
	 * @param string $content
	 * @param string $expectedResult
	 * @dataProvider makePlainReturnStringDataProvider
	 * @return void
	 * @test
	 */
	public function makePlainReturnString($content, $expectedResult) {
		$result = $this->generalValidatorMock->_call('makePlain', $content);
		$this->assertSame($expectedResult, $result);
	}
}