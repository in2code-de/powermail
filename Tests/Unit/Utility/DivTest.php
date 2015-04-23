<?php
namespace In2code\Powermail\Tests\Utility;

use \TYPO3\CMS\Core\Tests\UnitTestCase,
	\In2code\Powermail\Utility\Div,
	\In2code\Powermail\Domain\Model\Answer,
	\In2code\Powermail\Domain\Model\Field,
	\In2code\Powermail\Domain\Model\Mail;

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
 * Div Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class DivTest extends UnitTestCase {

	/**
	 * @var \In2code\Powermail\Utility\Div
	 */
	protected $generalValidatorMock;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->generalValidatorMock = $this->getAccessibleMock(
			'\In2code\Powermail\Utility\Div',
			array('dummy')
		);
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
		$this->generalValidatorMock->_set('objectManager', $objectManager);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->generalValidatorMock);
	}

	/**
	 * Dataprovider getSenderNameFromArgumentsReturnsString()
	 *
	 * @return array
	 */
	public function getSenderNameFromArgumentsReturnsStringDataProvider() {
		return array(
			array(
				array(
					'Alex',
					'Kellner'
				),
				NULL,
				NULL,
				'Alex Kellner'
			),
			array(
				array(
					'Prof. Dr.',
					'Müller'
				),
				'abc',
				'def',
				'Prof. Dr. Müller'
			),
			array(
				NULL,
				NULL,
				'Fallback Name',
				'Fallback Name'
			),
			array(
				NULL,
				'Fallback Name',
				NULL,
				'Fallback Name'
			),
			array(
				NULL,
				NULL,
				NULL,
				'No Sendername'
			),
			array(
				array(
					// test multivalue (e.g. checkbox)
					array(
						'Prof.',
						'Dr.'
					),
					'Max',
					'Muster'
				),
				'xyz',
				'abc',
				'Prof. Dr. Max Muster'
			),
		);
	}

	/**
	 * Test for getSenderNameFromArguments()
	 *
	 * @param array $values
	 * @param string $fallback
	 * @param string $defaultMailFromAddress
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getSenderNameFromArgumentsReturnsStringDataProvider
	 * @test
	 */
	public function getSenderNameFromArgumentsReturnsString($values, $fallback, $defaultMailFromAddress, $expectedResult) {
		$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = $defaultMailFromAddress;
		$mail = new Mail;
		if (is_array($values)) {
			foreach ($values as $value) {
				$answer = new Answer;
				$field = new Field;
				$field->setType('input');
				$field->setSenderName(TRUE);
				$answer->setValue($value);
				$answer->setValueType((is_array($value) ? 1 : 0));
				$answer->setField($field);
				$mail->addAnswer($answer);
			}
		}

		$result = $this->generalValidatorMock->_callRef('getSenderNameFromArguments', $mail, $fallback);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider getSenderMailFromArgumentsReturnsString()
	 *
	 * @return array
	 */
	public function getSenderMailFromArgumentsReturnsStringDataProvider() {
		return array(
			array(
				array(
					'no email',
					'abc@def.gh'
				),
				NULL,
				NULL,
				'abc@def.gh'
			),
			array(
				array(
					'alexander.kellner@in2code.de',
					'abc@def.gh'
				),
				NULL,
				NULL,
				'alexander.kellner@in2code.de'
			),
			array(
				array(
					'no email'
				),
				'test1@email.org',
				'test2@email.org',
				'test1@email.org'
			),
			array(
				array(
					'no email'
				),
				'test1@email.org',
				NULL,
				'test1@email.org'
			),
			array(
				array(
					'no email'
				),
				NULL,
				'test2@email.org',
				'test2@email.org'
			),
			array(
				array(
					'abc',
					'def',
					'ghi'
				),
				'test1@email.org',
				'test2@email.org',
				'test1@email.org'
			)
		);
	}

	/**
	 * Test for getSenderMailFromArguments()
	 *
	 * @param array $values
	 * @param string $fallback
	 * @param string $defaultMailFromAddress
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getSenderMailFromArgumentsReturnsStringDataProvider
	 * @test
	 */
	public function getSenderMailFromArgumentsReturnsString($values, $fallback, $defaultMailFromAddress, $expectedResult) {
		$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = $defaultMailFromAddress;
		$mail = new Mail;
		if (is_array($values)) {
			foreach ($values as $value) {
				$answer = new Answer;
				$field = new Field;
				$field->setType('input');
				$field->setSenderEmail(TRUE);
				$answer->setValue($value);
				$answer->setField($field);
				$mail->addAnswer($answer);
			}
		}

		$result = $this->generalValidatorMock->_callRef('getSenderMailFromArguments', $mail, $fallback);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Test for getStoragePage()
	 *
	 * @return void
	 * @test
	 */
	public function getStoragePageReturnsInt() {
		$result = Div::getStoragePage(123);
		$this->assertSame(123, $result);
	}

	/**
	 * Dataprovider getVariablesWithMarkersFromMailReturnsArray()
	 *
	 * @return array
	 */
	public function getVariablesWithMarkersFromMailReturnsArrayDataProvider() {
		return array(
			array(
				array(
					array(
						'marker',
						'value'
					),
				),
				array(
					'marker' => 'value'
				),
			),
			array(
				array(
					array(
						'firstname',
						'Alex'
					),
					array(
						'lastname',
						'Kellner'
					),
					array(
						'email',
						'alex@in2code.de'
					),
				),
				array(
					'firstname' => 'Alex',
					'lastname' => 'Kellner',
					'email' => 'alex@in2code.de'
				),
			),
			array(
				array(
					array(
						'checkbox',
						array(
							'red',
							'blue'
						)
					),
					array(
						'firstname',
						'Alex'
					),
				),
				array(
					'checkbox' => 'red, blue',
					'firstname' => 'Alex'
				),
			),
		);
	}

	/**
	 * Test for getVariablesWithMarkersFromMail()
	 *
	 * @param array $values
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getVariablesWithMarkersFromMailReturnsArrayDataProvider
	 * @test
	 */
	public function getVariablesWithMarkersFromMailReturnsArray($values, $expectedResult) {
		$mail = new Mail;
		if (is_array($values)) {
			foreach ($values as $markerValueMix) {
				$answer = new Answer;
				$field = new Field;
				$field->setMarker($markerValueMix[0]);
				$answer->setValue($markerValueMix[1]);
				$answer->setField($field);
				$answer->setValueType((is_array($markerValueMix[1]) ? 1 : 0));
				$mail->addAnswer($answer);
			}
		}

		$result = $this->generalValidatorMock->_callRef('getVariablesWithMarkersFromMail', $mail);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider getLabelsWithMarkersFromMailReturnsArray()
	 *
	 * @return array
	 */
	public function getLabelsWithMarkersFromMailReturnsArrayDataProvider() {
		return array(
			array(
				array(
					array(
						'marker',
						'title'
					),
				),
				array(
					'label_marker' => 'title'
				),
			),
			array(
				array(
					array(
						'firstname',
						'Firstname'
					),
					array(
						'lastname',
						'Lastname'
					),
					array(
						'email',
						'Email Address'
					),
				),
				array(
					'label_firstname' => 'Firstname',
					'label_lastname' => 'Lastname',
					'label_email' => 'Email Address'
				),
			),
		);
	}

	/**
	 * Test for getLabelsWithMarkersFromMail()
	 *
	 * @param array $values
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getLabelsWithMarkersFromMailReturnsArrayDataProvider
	 * @test
	 */
	public function getLabelsWithMarkersFromMailReturnsArray($values, $expectedResult) {
		$mail = new Mail;
		if (is_array($values)) {
			foreach ($values as $markerTitleMix) {
				$answer = new Answer;
				$field = new Field;
				$field->setMarker($markerTitleMix[0]);
				$field->setTitle($markerTitleMix[1]);
				$answer->setField($field);
				$mail->addAnswer($answer);
			}
		}

		$result = $this->generalValidatorMock->_callRef('getLabelsWithMarkersFromMail', $mail);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider getDataTypeFromFieldTypeReturnsInt()
	 *
	 * @return array
	 */
	public function getDataTypeFromFieldTypeReturnsIntDataProvider() {
		return array(
			array(
				'captcha',
				0
			),
			array(
				'check',
				1
			),
			array(
				'date',
				2
			),
			array(
				'file',
				3
			),
			array(
				'select',
				1
			),
			array(
				'input',
				0
			)
		);
	}

	/**
	 * Test for getDataTypeFromFieldType()
	 *
	 * @param \string $value
	 * @param \int $expectedResult
	 * @return void
	 * @dataProvider getDataTypeFromFieldTypeReturnsIntDataProvider
	 * @test
	 */
	public function getDataTypeFromFieldTypeReturnsInt($value, $expectedResult) {
		$result = Div::getDataTypeFromFieldType($value);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider optionArrayReturnsArray()
	 *
	 * @return array
	 */
	public function optionArrayReturnsArrayDataProvider() {
		return array(
			array(
				'abc',
				array(
					array(
						'label' => 'abc',
						'value' => 'abc',
						'selected' => 0
					),
				)
			),
			array(
				"red\nblue\nyellow",
				array(
					array(
						'label' => 'red',
						'value' => 'red',
						'selected' => 0
					),
					array(
						'label' => 'blue',
						'value' => 'blue',
						'selected' => 0
					),
					array(
						'label' => 'yellow',
						'value' => 'yellow',
						'selected' => 0
					),
				)
			),
			array(
				"please choose...|\nred\nblue|blue|*",
				array(
					array(
						'label' => 'please choose...',
						'value' => '',
						'selected' => 0
					),
					array(
						'label' => 'red',
						'value' => 'red',
						'selected' => 0
					),
					array(
						'label' => 'blue',
						'value' => 'blue',
						'selected' => 1
					),
				)
			),
			array(
				"||*\nred|red shoes",
				array(
					array(
						'label' => '',
						'value' => '',
						'selected' => 1
					),
					array(
						'label' => 'red',
						'value' => 'red shoes',
						'selected' => 0
					),
				)
			),
		);
	}

	/**
	 * Test for optionArray()
	 *
	 * @param string $value
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider optionArrayReturnsArrayDataProvider
	 * @test
	 */
	public function optionArrayReturnsArray($value, $expectedResult) {
		$result = Div::optionArray($value, '');
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Dataprovider isNotEmptyReturnsBool()
	 *
	 * @return array
	 */
	public function isNotEmptyReturnsBoolDataProvider() {
		return array(
			'string "in2code.de"' => array(
				'in2code.de',
				TRUE
			),
			'string "a"' => array(
				'a',
				TRUE
			),
			'string empty' => array(
				'',
				FALSE
			),
			'string "0"' => array(
				'0',
				TRUE
			),
			'int 0' => array(
				0,
				TRUE
			),
			'int 1' => array(
				1,
				TRUE
			),
			'float 0.0' => array(
				0.0,
				TRUE
			),
			'float 1.0' => array(
				1.0,
				TRUE
			),
			'null' => array(
				NULL,
				FALSE
			),
			'bool false' => array(
				FALSE,
				FALSE
			),
			'bool true' => array(
				TRUE,
				FALSE
			),
			'array: string empty' => array(
				array(''),
				FALSE
			),
			'array: int 0' => array(
				array(0),
				TRUE
			),
			'array: int 1' => array(
				array(1),
				TRUE
			),
			'array: "abc" => "def"' => array(
				array('abc' => 'def'),
				TRUE
			),
			'array: empty' => array(
				array(),
				FALSE
			),
		);
	}

	/**
	 * Test for isNotEmpty()
	 *
	 * @param string $value
	 * @param array $expectedResult
	 * @return void
	 * @dataProvider isNotEmptyReturnsBoolDataProvider
	 * @test
	 */
	public function isNotEmptyReturnsBool($value, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			Div::isNotEmpty($value)
		);
	}

	/**
	 * Test for createHash()
	 *
	 * @return void
	 * @test
	 */
	public function createHashReturnsString() {
		$value = 'abc';
		$result = $this->generalValidatorMock->_callRef('createHash', $value);
		$this->assertFalse(($value === $result));
		$this->assertTrue(strlen($result) === 10);
	}

	/**
	 * Dataprovider getSubFolderOfCurrentUrlReturnsString()
	 *
	 * @return array
	 */
	public function getSubFolderOfCurrentUrlReturnsStringDataProvider() {
		return array(
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				'/'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/',
				''
			),
			array(
				TRUE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder/'
			),
			array(
				FALSE,
				TRUE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder/'
			),
			array(
				TRUE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'/subfolder'
			),
			array(
				FALSE,
				FALSE,
				'http://www.in2code.de',
				'http://www.in2code.de/subfolder/',
				'subfolder'
			),
		);
	}

	/**
	 * Test for getSubFolderOfCurrentUrl()
	 *
	 * @param bool $leadingSlash will be prepended
	 * @param bool $trailingSlash will be appended
	 * @param string $host
	 * @param string $url
	 * @param string $expectedResult
	 * @return void
	 * @dataProvider getSubFolderOfCurrentUrlReturnsStringDataProvider
	 * @test
	 */
	public function getSubFolderOfCurrentUrlReturnsString($leadingSlash, $trailingSlash, $host, $url, $expectedResult) {
		$result = Div::getSubFolderOfCurrentUrl($leadingSlash, $trailingSlash, $host, $url);
		$this->assertSame($expectedResult, $result);
	}

	/**
	 * Data Provider for createRandomStringAlwaysReturnsStringsOfGivenLength
	 *
	 * @return array
	 */
	public function createRandomStringAlwaysReturnsStringsOfGivenLengthDataProvider() {
		return array(
			'default params' => array(
				32,
				TRUE,
			),
			'default length lowercase' => array(
				32,
				FALSE,
			),
			'60 length' => array(
				60,
				TRUE,
			),
			'60 length lowercase' => array(
				60,
				FALSE,
			),
		);
	}

	/**
	 * createRandomStringAlwaysReturnsStringsOfGivenLength Test
	 *
	 * @param int $length
	 * @param bool $uppercase
	 * @dataProvider createRandomStringAlwaysReturnsStringsOfGivenLengthDataProvider
	 * @return void
	 * @test
	 */
	public function createRandomStringAlwaysReturnsStringsOfGivenLength($length, $uppercase) {
		for ($i = 0; $i < 100; $i++) {
			$string = Div::createRandomString($length, $uppercase);

			$regex = '~[a-z0-9]{' . $length . '}~';
			if ($uppercase) {
				$regex = '~[a-zA-Z0-9]{' . $length . '}~';
			}

			$this->assertSame(1, preg_match($regex, $string));
		}
	}

	/**
	 * Data Provider for isJsonArrayReturnsBool()
	 *
	 * @return array
	 */
	public function isJsonArrayReturnsBoolDataProvider() {
		return array(
			array(
				json_encode(array('a')),
				TRUE
			),
			array(
				json_encode('a,b:c'),
				FALSE
			),
			array(
				json_encode(array('object' => 'a')),
				TRUE
			),
			array(
				json_encode(array(array('title' => 'test2'), array('title' => 'test2'))),
				TRUE
			),
			array(
				'a,b:c',
				FALSE
			),
		);
	}

	/**
	 * isJsonArray Test
	 *
	 * @param string $value
	 * @param bool $expectedResult
	 * @dataProvider isJsonArrayReturnsBoolDataProvider
	 * @return void
	 * @test
	 */
	public function isJsonArrayReturnsBool($value, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			Div::isJsonArray($value)
		);
	}

	/**
	 * Data Provider for isBackendAdminReturnsBool()
	 *
	 * @return array
	 */
	public function isBackendAdminReturnsBoolDataProvider() {
		return array(
			array(
				1,
				TRUE
			),
			array(
				0,
				FALSE
			),
		);
	}

	/**
	 * isBackendAdmin Test
	 *
	 * @param string $value
	 * @param bool $expectedResult
	 * @dataProvider isBackendAdminReturnsBoolDataProvider
	 * @return void
	 * @test
	 */
	public function isBackendAdminReturnsBool($value, $expectedResult) {
		$GLOBALS['BE_USER']->user['admin'] = $value;
		$this->assertSame(
			$expectedResult,
			Div::isBackendAdmin()
		);
	}

	/**
	 * Data Provider for getDomainFromUriReturnsString()
	 *
	 * @return array
	 */
	public function getDomainFromUriReturnsStringDataProvider() {
		return array(
			array(
				'http://subdomain.domain.org/folder/file.html',
				'subdomain.domain.org'
			),
			array(
				'ftp://domain.org',
				'domain.org'
			),
			array(
				'https://www.domain.co.uk/',
				'www.domain.co.uk'
			),
		);
	}

	/**
	 * getDomainFromUri Test
	 *
	 * @param string $value
	 * @param string $expectedResult
	 * @dataProvider getDomainFromUriReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getDomainFromUriReturnsString($value, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			Div::getDomainFromUri($value)
		);
	}

	/**
	 * Data Provider for getCountryFromIpReturnsString()
	 *
	 * @return array
	 */
	public function getCountryFromIpReturnsStringDataProvider() {
		return array(
			array(
				'217.72.208.133',
				'Germany'
			),
			array(
				'27.121.255.4',
				'Japan'
			),
			array(
				'5.226.31.255',
				'Spain'
			),
			array(
				'66.85.131.18',
				'United States'
			),
			array(
				'182.118.23.7',
				'China'
			),
		);
	}

	/**
	 * getCountryFromIp Test
	 *
	 * @param string $ip
	 * @param string $expectedResult
	 * @dataProvider getCountryFromIpReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getCountryFromIpReturnsString($ip, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			Div::getCountryFromIp($ip)
		);
	}

	/**
	 * Data Provider for getImageSourceFromTagReturnsString()
	 *
	 * @return array
	 */
	public function getImageSourceFromTagReturnsStringDataProvider() {
		return array(
			array(
				array(
					'<img class="tx-srfreecap-image" id="tx_srfreecap_captcha_image_6ac99" ',
					'src="http://powermail.localhost.de/index.php?eID=sr_freecap_EidDispatcher&amp;',
					'id=111&amp;extensionName=SrFreecap&amp;pluginName=ImageGenerator&amp;controllerName=ImageGenerator',
					'&amp;actionName=show&amp;formatName=png&amp;set=6ac99" alt="CAPTCHA-Bild zum Spam-Schutz "/>',
					'<span class="tx-srfreecap-cant-read">Wenn Sie das Wort nicht lesen können, ',
					'<a href="#" onclick="this.blur();SrFreecap.newImage(\'6ac99\', \'Entschuldigung, wir können nicht ',
					'automatisch ein neues Bild zeigen. Schicken Sie das Formular ab und ein neues Bild wird geladen.\');',
					'return false;">bitte hier klicken</a>.</span>'
				),
				array(
					'http://powermail.localhost.de/index.php?eID=sr_freecap_EidDispatcher&amp;',
					'id=111&amp;extensionName=SrFreecap&amp;pluginName=ImageGenerator&amp;controllerName=ImageGenerator',
					'&amp;actionName=show&amp;formatName=png&amp;set=6ac99'
				)
			),
			array(
				array(
					'abcd <img src="/abc/pic.png" /> adsa ',
				),
				array(
					'/abc/pic.png'
				)
			),
			array(
				array(
					'<b> <img src="http://d.org/pic.bmp" /> ads <img src="xyz.php" /> </b>adsa',
				),
				array(
					'http://d.org/pic.bmp'
				)
			),
		);
	}

	/**
	 * getImageSourceFromTag Test
	 *
	 * @param array $html
	 * @param array $expectedResult
	 * @dataProvider getImageSourceFromTagReturnsStringDataProvider
	 * @return void
	 * @test
	 */
	public function getImageSourceFromTagReturnsString($html, $expectedResult) {
		$this->assertSame(
			implode('', $expectedResult),
			Div::getImageSourceFromTag(implode('', $html))
		);
	}

	/**
	 * Data Provider for conditionalVariableReturnsMixed()
	 *
	 * @return array
	 */
	public function conditionalVariableReturnsMixedDataProvider() {
		return array(
			array(
				'string',
				'fallbackstring',
				'string'
			),
			array(
				array('abc'),
				array('def'),
				array('abc')
			),
			array(
				'',
				'fallback',
				'fallback'
			),
			array(
				NULL,
				TRUE,
				TRUE
			),
			array(
				123,
				234,
				123
			)
		);
	}

	/**
	 * conditionalVariable Test
	 *
	 * @param mixed $variable
	 * @param mixed $fallback
	 * @param mixed $expectedResult
	 * @dataProvider conditionalVariableReturnsMixedDataProvider
	 * @return void
	 * @test
	 */
	public function conditionalVariableReturnsMixed($variable, $fallback, $expectedResult) {
		$this->assertSame(
			$expectedResult,
			Div::conditionalVariable($variable, $fallback)
		);
	}
}