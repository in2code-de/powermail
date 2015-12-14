<?php
namespace In2code\Powermail\Tests\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\Answer;
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
 * SpamShieldValidator Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SpamShieldValidatorTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShieldValidator
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Validator\SpamShieldValidator',
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider calculateMailSpamFactorReturnsVoid()
     *
     * @return array
     */
    public function calculateMailSpamFactorReturnsVoidDataProvider()
    {
        return [
            'indication of 0' => [
                0,
                0.000
            ],
            'indication of 1' => [
                1,
                0.000
            ],
            'indication of 2' => [
                2,
                0.5
            ],
            'indication of 5' => [
                5,
                0.8
            ],
            'indication of 8' => [
                8,
                0.8750
            ],
            'indication of 12' => [
                12,
                0.9167
            ],
            'indication of 50' => [
                50,
                0.9800
            ],
            'indication of 50050' => [
                50050,
                1.000
            ],
        ];
    }

    /**
     * Test for calculateMailSpamFactor()
     *
     * @param int $spamIndicator
     * @param float $expectedCalculateMailSpamFactor
     * @return void
     * @dataProvider calculateMailSpamFactorReturnsVoidDataProvider
     * @test
     */
    public function calculateMailSpamFactorReturnsVoid($spamIndicator, $expectedCalculateMailSpamFactor)
    {
        $this->generalValidatorMock->_callRef('setSpamIndicator', $spamIndicator);
        $this->generalValidatorMock->_callRef('calculateMailSpamFactor');
        $this->assertSame(
            number_format($expectedCalculateMailSpamFactor, 4),
            number_format($this->generalValidatorMock->_callRef('getCalculatedMailSpamFactor'), 4)
        );
    }

    /**
     * Dataprovider honeypodCheckReturnsVoid()
     *
     * @return array
     */
    public function honeypodCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 1, pot filled' => [
                1,
                'abc',
                1
            ],
            'indication of 3, pot filled' => [
                3,
                '@test',
                3
            ],
            'indication of 2, pot empty' => [
                2,
                '',
                0
            ],
        ];
    }

    /**
     * Test for honeypodCheck()
     *
     * @param int $spamIndicator
     * @param string $pot if $piVars['field']['__hp'] filled
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider honeypodCheckReturnsVoidDataProvider
     * @test
     */
    public function honeypodCheckReturnsVoid($spamIndicator, $pot, $expectedOverallSpamIndicator)
    {
        $this->generalValidatorMock->_set('piVars', ['field' => ['__hp' => $pot]]);
        $this->generalValidatorMock->_callRef('honeypodCheck', $spamIndicator);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider linkCheckReturnsVoid()
     *
     * @return array
     */
    public function linkCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 1, links allowed 1, 2 links given' => [
                1,
                1,
                'xx <a href="http://www.test.de">http://www.test.de</a> xx',
                1
            ],
            'indication of 7, links allowed 3, 2 links given' => [
                7,
                3,
                'xx <a href="ftp://www.test.de">https://www.test.de</a> xx',
                0
            ],
            'indication of 7, links allowed 0, 1 link given' => [
                7,
                0,
                'xx <a href="#">https://www.test.de</a> xx',
                7
            ],
            'indication of 2, links allowed 2, 3 link given' => [
                2,
                2,
                'xx [url=http://www.xyz.org]http://www.xyz.org[/url] http://www.xyz.org xx',
                2
            ],
        ];
    }

    /**
     * Test for linkCheck()
     *
     * @param int $spamIndicator
     * @param int $allowedLinks
     * @param string $text
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider linkCheckReturnsVoidDataProvider
     * @test
     */
    public function linkCheckReturnsVoid($spamIndicator, $allowedLinks, $text, $expectedOverallSpamIndicator)
    {
        $mail = new Mail();
        $answer = new Answer();
        $answer->setValueType(0);
        $answer->setValue($text);
        $mail->addAnswer($answer);

        $this->generalValidatorMock->_callRef('linkCheck', $mail, $spamIndicator, $allowedLinks);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider nameCheckReturnsVoid()
     *
     * @return array
     */
    public function nameCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 0, same first- and lastname' => [
                0,
                [
                    'firstname' => 'abcdef',
                    'lastname' => 'abcdef',
                    'xyz' => '123',
                ],
                0
            ],
            'indication of 3, same first- and lastname' => [
                3,
                [
                    'firstname' => 'abcdef',
                    'lastname' => 'abcdef',
                    'xyz' => '123',
                ],
                3
            ],
            'indication of 7, different values' => [
                7,
                [
                    'firstnamex' => 'abcdef',
                    'lastname' => 'abcdef',
                    'xyz' => '123',
                ],
                0
            ],
            'indication of 7, same first- and lastname' => [
                7,
                [
                    'first_name' => 'viagra',
                    'surname' => 'viagra',
                    'xyz' => '123',
                ],
                7
            ],
        ];
    }

    /**
     * Test for nameCheck()
     *
     * @param int $spamIndicator
     * @param array $answerProperties
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider nameCheckReturnsVoidDataProvider
     * @test
     */
    public function nameCheckReturnsVoid($spamIndicator, $answerProperties, $expectedOverallSpamIndicator)
    {
        $mail = new Mail();
        foreach ($answerProperties as $fieldName => $value) {
            $field = new Field();
            $field->setMarker($fieldName);
            $answer = new Answer();
            $answer->setField($field);
            $answer->_setProperty('value', $value);
            $answer->setValueType(132);
            $mail->addAnswer($answer);
        }

        $this->generalValidatorMock->_callRef('nameCheck', $mail, $spamIndicator);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider sessionCheckReturnsVoid()
     *
     * @return array
     */
    public function sessionCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 0, time given' => [
                0,
                1234,
                0
            ],
            'indication of 3, time given' => [
                3,
                1234,
                0
            ],
            'indication of 3, no time given' => [
                3,
                '',
                3
            ],
            'indication of 4, no time given' => [
                4,
                0,
                4
            ],
            'indication of 5, no time given' => [
                5,
                null,
                5
            ],
        ];
    }

    /**
     * Test for sessionCheck()
     *
     * @param int $spamIndicator
     * @param int $timeFromSession
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider sessionCheckReturnsVoidDataProvider
     * @test
     */
    public function sessionCheckReturnsVoid($spamIndicator, $timeFromSession, $expectedOverallSpamIndicator)
    {
        $this->generalValidatorMock->_callRef('sessionCheck', $spamIndicator, $timeFromSession);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider uniqueCheckReturnsVoid()
     *
     * @return array
     */
    public function uniqueCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 0, duplicated values' => [
                0,
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                0
            ],
            'indication of 5, duplicated values' => [
                5,
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                5
            ],
            'indication of 6, duplicated values' => [
                5,
                [
                    'alexander',
                    'kellner',
                    'alexander.kellner@test.org',
                    'This is an example text',
                    [
                        'abc',
                        'def'
                    ]
                ],
                0
            ],
        ];
    }

    /**
     * Test for uniqueCheck()
     *
     * @param int $spamIndicator
     * @param array $answerProperties
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider uniqueCheckReturnsVoidDataProvider
     * @test
     */
    public function uniqueCheckReturnsVoid($spamIndicator, $answerProperties, $expectedOverallSpamIndicator)
    {
        $mail = new Mail();
        foreach ($answerProperties as $value) {
            $answer = new Answer();
            $answer->setValueType(123);
            $answer->setValue($value);
            $mail->addAnswer($answer);
        }

        $this->generalValidatorMock->_callRef('uniqueCheck', $mail, $spamIndicator);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider blacklistStringCheckReturnsVoid()
     *
     * @return array
     */
    public function blacklistStringCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 0, blacklisted values' => [
                0,
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                'abcdef,123,xyz',
                0
            ],
            'indication of 3, blacklisted values' => [
                3,
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                'abcdef,123,xyz',
                3
            ],
            'indication of 7, blacklisted values' => [
                7,
                [
                    'buy cheap v!agra now',
                    'all is fine',
                ],
                'viagra   ,  v!agra  , v1agra',
                7
            ],
            'indication of 1, not blacklisted values' => [
                7,
                [
                    'Staatsexamen',
                    'all is fine',
                ],
                'sex',
                0
            ],
        ];
    }

    /**
     * Test for blacklistStringCheck()
     *
     * @param int $spamIndicator
     * @param array $answerProperties
     * @param string $blacklist
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider blacklistStringCheckReturnsVoidDataProvider
     * @test
     */
    public function blacklistStringCheckReturnsVoid(
        $spamIndicator,
        $answerProperties,
        $blacklist,
        $expectedOverallSpamIndicator
    ) {
        $mail = new Mail();
        foreach ($answerProperties as $value) {
            $answer = new Answer();
            $answer->setValueType(123);
            $answer->setValue($value);
            $mail->addAnswer($answer);
        }
        $this->generalValidatorMock->_set(
            'settings',
            [
                'spamshield.' => [
                    'indicator.' => [
                        'blacklistStringValues' => $blacklist
                    ]
                ]
            ]
        );
        $this->generalValidatorMock->_callRef('blacklistStringCheck', $mail, $spamIndicator);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider blacklistIpCheckReturnsVoid()
     *
     * @return array
     */
    public function blacklistIpCheckReturnsVoidDataProvider()
    {
        return [
            'indication of 0, blacklisted ip' => [
                0,
                '123.123.123.123',
                '123.123.123.123',
                0
            ],
            'indication of 3, blacklisted ip' => [
                3,
                '123.123.123.123',
                '123.123.123.123',
                3
            ],
            'indication of 4, blacklisted ip' => [
                4,
                ',23.123.123.12,',
                '23.123.123.12',
                4
            ],
            'indication of 5, blacklisted ip' => [
                4,
                '192.168.0.2 , 		23.166.12.12 , 250.182.0.3',
                '23.166.12.12',
                4
            ],
            'indication of 6, no blacklisted ip' => [
                4,
                '192.168.0.2 , 		23.166.12.12 , 250.182.0.3',
                '23.166.12.1',
                0
            ],
        ];
    }

    /**
     * Test for blacklistIpCheck()
     *
     * @param int $spamIndicator
     * @param string $blacklist
     * @param string $userIp
     * @param int $expectedOverallSpamIndicator
     * @return void
     * @dataProvider blacklistIpCheckReturnsVoidDataProvider
     * @test
     */
    public function blacklistIpCheckReturnsVoid($spamIndicator, $blacklist, $userIp, $expectedOverallSpamIndicator)
    {
        $this->generalValidatorMock->_set(
            'settings',
            [
                'spamshield.' => [
                    'indicator.' => [
                        'blacklistIpValues' => $blacklist
                    ]
                ]
            ]
        );
        $this->generalValidatorMock->_callRef('blacklistIpCheck', $spamIndicator, $userIp);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('getSpamIndicator'));
    }

    /**
     * Dataprovider formatSpamFactorReturnsString()
     *
     * @return array
     */
    public function formatSpamFactorReturnsStringDataProvider()
    {
        return [
            [
                0.23,
                '23%',
            ],
            [
                0.0,
                '0%',
            ],
            [
                1.0,
                '100%',
            ],
            [
                0.999999999,
                '100%',
            ],
        ];
    }

    /**
     * Test for formatSpamFactor()
     *
     * @param float $factor
     * @param string $expectedResult
     * @return void
     * @dataProvider formatSpamFactorReturnsStringDataProvider
     * @test
     */
    public function formatSpamFactorReturnsString($factor, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('formatSpamFactor', $factor));
    }

    /**
     * Dataprovider isSpamToleranceLimitReachedReturnsBool()
     *
     * @return array
     */
    public function isSpamToleranceLimitReachedReturnsBoolDataProvider()
    {
        return [
            [
                0.8,
                0.9,
                false
            ],
            [
                0.5312,
                0.54,
                false
            ],
            [
                0.9,
                0.8,
                true
            ],
            [
                0.0,
                0.0,
                true
            ],
            [
                0.01,
                0.0,
                true
            ],
            [
                1.0,
                1.0,
                true
            ],
        ];
    }

    /**
     * Test for isSpamToleranceLimitReached()
     *
     * @param float $calculatedMailSpamFactor
     * @param float $spamFactorLimit
     * @param bool $expectedResult
     * @return void
     * @dataProvider isSpamToleranceLimitReachedReturnsBoolDataProvider
     * @test
     */
    public function isSpamToleranceLimitReachedReturnsBool($calculatedMailSpamFactor, $spamFactorLimit, $expectedResult)
    {
        $this->generalValidatorMock->_set('calculatedMailSpamFactor', $calculatedMailSpamFactor);
        $this->generalValidatorMock->_set('spamFactorLimit', $spamFactorLimit);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('isSpamToleranceLimitReached'));
    }

    /**
     * Dataprovider findStringInStringReturnsBool()
     *
     * @return array
     */
    public function findStringInStringReturnsBoolDataProvider()
    {
        return [
            [
                'Sex',
                'sex',
                true
            ],
            [
                'bar sex foo',
                'sex',
                true
            ],
            [
                'Staatsexamen',
                'sex',
                false
            ],
            [
                '_sex_foo',
                'sex',
                true
            ],
            [
                'foo.sex.bar.foo',
                'sex',
                true
            ],
        ];
    }

    /**
     * Test for findStringInString()
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $expectedResult
     * @return void
     * @dataProvider findStringInStringReturnsBoolDataProvider
     * @test
     */
    public function findStringInStringReturnsBool($haystack, $needle, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            $this->generalValidatorMock->_callRef('findStringInString', $haystack, $needle)
        );
    }
}
