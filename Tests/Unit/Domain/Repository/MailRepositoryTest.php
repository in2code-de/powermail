<?php
namespace In2code\Powermail\Tests\Domain\Model;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * MailRepository Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class MailRepositoryTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface');
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Repository\MailRepository',
            ['dummy'],
            [$objectManager]
        );
    }

    /**
     * Dataprovider getLabelsWithMarkersFromMailReturnsArray()
     *
     * @return array
     */
    public function getLabelsWithMarkersFromMailReturnsArrayDataProvider()
    {
        return [
            [
                [
                    [
                        'marker',
                        'title'
                    ],
                ],
                [
                    'label_marker' => 'title'
                ],
            ],
            [
                [
                    [
                        'firstname',
                        'Firstname'
                    ],
                    [
                        'lastname',
                        'Lastname'
                    ],
                    [
                        'email',
                        'Email Address'
                    ],
                ],
                [
                    'label_firstname' => 'Firstname',
                    'label_lastname' => 'Lastname',
                    'label_email' => 'Email Address'
                ],
            ],
        ];
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
    public function getLabelsWithMarkersFromMailReturnsArray($values, $expectedResult)
    {
        $mail = new Mail();
        if (is_array($values)) {
            foreach ($values as $markerTitleMix) {
                $answer = new Answer();
                $field = new Field();
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
     * Dataprovider getVariablesWithMarkersFromMailReturnsArray()
     *
     * @return array
     */
    public function getVariablesWithMarkersFromMailReturnsArrayDataProvider()
    {
        return [
            [
                [
                    [
                        'marker',
                        'value'
                    ],
                ],
                [
                    'marker' => 'value'
                ],
            ],
            [
                [
                    [
                        'firstname',
                        'Alex'
                    ],
                    [
                        'lastname',
                        'Kellner'
                    ],
                    [
                        'email',
                        'alex@in2code.de'
                    ],
                ],
                [
                    'firstname' => 'Alex',
                    'lastname' => 'Kellner',
                    'email' => 'alex@in2code.de'
                ],
            ],
            [
                [
                    [
                        'checkbox',
                        [
                            'red',
                            'blue'
                        ]
                    ],
                    [
                        'firstname',
                        'Alex'
                    ],
                ],
                [
                    'checkbox' => 'red, blue',
                    'firstname' => 'Alex'
                ],
            ],
        ];
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
    public function getVariablesWithMarkersFromMailReturnsArray($values, $expectedResult)
    {
        $mail = new Mail;
        if (is_array($values)) {
            foreach ($values as $markerValueMix) {
                $answer = new Answer;
                $field = new Field;
                $field->setMarker($markerValueMix[0]);
                $answer->setValue($markerValueMix[1]);
                $answer->setValueType((is_array($markerValueMix[1]) ? 1 : 0));
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $this->generalValidatorMock->_callRef('disableSignals');
        $result = $this->generalValidatorMock->_callRef('getVariablesWithMarkersFromMail', $mail);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider getSenderMailFromArgumentsReturnsString()
     *
     * @return array
     */
    public function getSenderMailFromArgumentsReturnsStringDataProvider()
    {
        return [
            [
                [
                    'no email',
                    'abc@def.gh'
                ],
                null,
                null,
                'abc@def.gh'
            ],
            [
                [
                    'alexander.kellner@in2code.de',
                    'abc@def.gh'
                ],
                null,
                null,
                'alexander.kellner@in2code.de'
            ],
            [
                [
                    'no email'
                ],
                'test1@email.org',
                'test2@email.org',
                'test1@email.org'
            ],
            [
                [
                    'no email'
                ],
                'test1@email.org',
                null,
                'test1@email.org'
            ],
            [
                [
                    'no email'
                ],
                null,
                'test2@email.org',
                'test2@email.org'
            ],
            [
                [
                    'abc',
                    'def',
                    'ghi'
                ],
                'test1@email.org',
                'test2@email.org',
                'test1@email.org'
            ]
        ];
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
    public function getSenderMailFromArgumentsReturnsString(
        $values,
        $fallback,
        $defaultMailFromAddress,
        $expectedResult
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = $defaultMailFromAddress;
        $mail = new Mail;
        if (is_array($values)) {
            foreach ($values as $value) {
                $answer = new Answer;
                $answer->_setProperty('value', $value);
                $answer->_setProperty('valueType', (is_array($values) ? 2 : 0));
                $field = new Field;
                $field->setType('input');
                $field->setSenderEmail(true);
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $result = $this->generalValidatorMock->_callRef('getSenderMailFromArguments', $mail, $fallback);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Dataprovider getSenderNameFromArgumentsReturnsString()
     *
     * @return array
     */
    public function getSenderNameFromArgumentsReturnsStringDataProvider()
    {
        return [
            [
                [
                    'Alex',
                    'Kellner'
                ],
                null,
                null,
                'Alex Kellner'
            ],
            [
                [
                    'Prof. Dr.',
                    'Müller'
                ],
                'abc',
                'def',
                'Prof. Dr. Müller'
            ],
            [
                null,
                null,
                'Fallback Name',
                'Fallback Name'
            ],
            [
                null,
                'Fallback Name',
                null,
                'Fallback Name'
            ],
            [
                [
                    // test multivalue (e.g. checkbox)
                    [
                        'Prof.',
                        'Dr.'
                    ],
                    'Max',
                    'Muster'
                ],
                'xyz',
                'abc',
                'Prof. Dr. Max Muster'
            ],
        ];
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
    public function getSenderNameFromArgumentsReturnsString(
        $values,
        $fallback,
        $defaultMailFromAddress,
        $expectedResult
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = $defaultMailFromAddress;
        $mail = new Mail;
        if (is_array($values)) {
            foreach ($values as $value) {
                $answer = new Answer;
                $answer->_setProperty('translateFormat', 'Y-m-d');
                $answer->_setProperty('valueType', (is_array($values) ? 2 : 0));
                $field = new Field;
                $field->setType('input');
                $field->setSenderName(true);
                $answer->_setProperty('value', $value);
                $answer->setValueType((is_array($value) ? 1 : 0));
                $answer->setField($field);
                $mail->addAnswer($answer);
            }
        }

        $result = $this->generalValidatorMock->_callRef('getSenderNameFromArguments', $mail, $fallback);
        $this->assertSame($expectedResult, $result);
    }
}
