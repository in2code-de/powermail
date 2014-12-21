<?php
namespace In2code\Powermail\Tests\ViewHelpers\Validation;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Domain\Model\Form;

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
 * EnableParsleyAndAjaxViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class EnableParsleyAndAjaxViewHelperTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $abstractValidationViewHelperMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->abstractValidationViewHelperMock = $this->getAccessibleMock(
			'\In2code\Powermail\ViewHelpers\Validation\EnableParsleyAndAjaxViewHelper',
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
	 * Dataprovider for render()
	 *
	 * @return array
	 */
	public function renderReturnsArrayDataProvider() {
		return array(
			'nativeAndClientAndAjaxAndNoAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					),
					'misc' => array(
						'ajaxSubmit' => '1'
					)
				),
				array(),
				array(
					'data-parsley-validate' => 'data-parsley-validate',
					'data-validate' => 'html5',
					'data-powermail-ajax' => 'true',
					'data-powermail-form' => 123,
				)
			),
			'clientAndAjaxAndNoAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '1'
					),
					'misc' => array(
						'ajaxSubmit' => '1'
					)
				),
				array(),
				array(
					'data-parsley-validate' => 'data-parsley-validate',
					'data-powermail-ajax' => 'true',
					'data-powermail-form' => 123,
				)
			),
			'nativeAndAjaxAndNoAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '0'
					),
					'misc' => array(
						'ajaxSubmit' => '1'
					)
				),
				array(),
				array(
					'data-validate' => 'html5',
					'data-powermail-ajax' => 'true',
					'data-powermail-form' => 123,
				)
			),
			'AjaxAndNoAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '0',
						'client' => '0'
					),
					'misc' => array(
						'ajaxSubmit' => '1'
					)
				),
				array(),
				array(
					'data-powermail-ajax' => 'true',
					'data-powermail-form' => 123,
				)
			),
			'nativeAndClientAndNoAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					),
					'misc' => array(
						'ajaxSubmit' => '0'
					)
				),
				array(),
				array(
					'data-parsley-validate' => 'data-parsley-validate',
					'data-validate' => 'html5'
				)
			),
			'nativeAndClientAndAjaxAndAdditionalAttributes' => array(
				array(
					'validation' => array(
						'native' => '1',
						'client' => '1'
					),
					'misc' => array(
						'ajaxSubmit' => '1'
					)
				),
				array(
					'www' => 'in2code.de',
					'email' => 'service@in2code.de',
					'data-uid' => 234
				),
				array(
					'www' => 'in2code.de',
					'email' => 'service@in2code.de',
					'data-uid' => 234,
					'data-parsley-validate' => 'data-parsley-validate',
					'data-validate' => 'html5',
					'data-powermail-ajax' => 'true',
					'data-powermail-form' => 123
				)
			),
		);
	}

	/**
	 * Test for render()
	 *
	 * @param array $settings
	 * @param array $additionalAttributes
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider renderReturnsArrayDataProvider
	 * @test
	 */
	public function renderReturnsArray($settings, $additionalAttributes, $expectedResult) {
		$form = new Form;
		$form->_setProperty('uid', 123);

		$this->abstractValidationViewHelperMock->_set('settings', $settings);
		$result = $this->abstractValidationViewHelperMock->_callRef('render', $form, $additionalAttributes);
		$this->assertSame($expectedResult, $result);
	}
}