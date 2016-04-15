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
     * Dataprovider getCalculatedSpamFactorReturnsVoid()
     *
     * @return array
     */
    public function getCalculatedSpamFactorReturnsVoidDataProvider()
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
     * Test for getCalculatedSpamFactor()
     *
     * @param int $spamIndicator
     * @param float $expectedCalculateMailSpamFactor
     * @return void
     * @dataProvider getCalculatedSpamFactorReturnsVoidDataProvider
     * @test
     */
    public function getCalculatedSpamFactorReturnsVoid($spamIndicator, $expectedCalculateMailSpamFactor)
    {
        $this->generalValidatorMock->_callRef('setSpamIndicator', $spamIndicator);
        $this->generalValidatorMock->_callRef('calculateMailSpamFactor');
        $this->assertSame(
            number_format($expectedCalculateMailSpamFactor, 4),
            number_format($this->generalValidatorMock->_callRef('getCalculatedSpamFactor'), 4)
        );
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
     * @param float $calculatedSpamFactor
     * @param float $spamFactorLimit
     * @param bool $expectedResult
     * @return void
     * @dataProvider isSpamToleranceLimitReachedReturnsBoolDataProvider
     * @test
     */
    public function isSpamToleranceLimitReachedReturnsBool($calculatedSpamFactor, $spamFactorLimit, $expectedResult)
    {
        $this->generalValidatorMock->_set('calculatedSpamFactor', $calculatedSpamFactor);
        $this->generalValidatorMock->_set('spamFactorLimit', $spamFactorLimit);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('isSpamToleranceLimitReached'));
    }
}
