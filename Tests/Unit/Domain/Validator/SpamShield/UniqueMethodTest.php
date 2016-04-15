<?php
namespace In2code\Powermail\Tests\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class UniqueMethodTest
 * @package In2code\Powermail\Tests\Domain\Validator\Spamshield
 */
class UniqueMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod',
            ['dummy'],
            [
                new Mail(),
                []
            ]
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
     * Dataprovider spamCheckReturnsVoid()
     *
     * @return array
     */
    public function spamCheckReturnsVoidDataProvider()
    {
        return [
            [
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                true
            ],
            [
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
                false
            ],
        ];
    }

    /**
     * Test for spamCheck()
     *
     * @param array $answerProperties
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     */
    public function spamCheckReturnsVoid($answerProperties, $expectedResult)
    {
        $mail = new Mail();
        foreach ($answerProperties as $value) {
            $answer = new Answer();
            $answer->setValueType(123);
            $answer->setValue($value);
            $mail->addAnswer($answer);
        }

        $this->generalValidatorMock->_set('mail', $mail);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
