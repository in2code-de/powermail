<?php

namespace In2code\Powermail\Tests\Unit\Domain\Validator;

use In2code\Powermail\Domain\Validator\SpamShieldValidator;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class SpamShieldValidatorTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShieldValidator
 */
class SpamShieldValidatorTest extends UnitTestCase
{
    /**
     * @var SpamShieldValidator
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            SpamShieldValidator::class,
            ['dummy'],
            [],
            '',
            false
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
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
                0.000,
            ],
            'indication of 1' => [
                1,
                0.000,
            ],
            'indication of 2' => [
                2,
                0.5,
            ],
            'indication of 5' => [
                5,
                0.8,
            ],
            'indication of 8' => [
                8,
                0.8750,
            ],
            'indication of 12' => [
                12,
                0.9167,
            ],
            'indication of 50' => [
                50,
                0.9800,
            ],
            'indication of 50050' => [
                50050,
                1.000,
            ],
        ];
    }

    /**
     * @param int $spamIndicator
     * @param float $expectedCalculateMailSpamFactor
     * @return void
     * @dataProvider getCalculatedSpamFactorReturnsVoidDataProvider
     * @test
     * @covers ::getCalculatedSpamFactor
     */
    public function getCalculatedSpamFactorReturnsVoid($spamIndicator, $expectedCalculateMailSpamFactor)
    {
        $this->generalValidatorMock->_callRef('setSpamIndicator', $spamIndicator);
        $this->generalValidatorMock->_callRef('calculateMailSpamFactor');
        self::assertSame(
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
     * @param float $factor
     * @param string $expectedResult
     * @return void
     * @dataProvider formatSpamFactorReturnsStringDataProvider
     * @test
     * @covers ::formatSpamFactor
     */
    public function formatSpamFactorReturnsString($factor, $expectedResult)
    {
        self::assertSame($expectedResult, $this->generalValidatorMock->_callRef('formatSpamFactor', $factor));
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
                false,
            ],
            [
                0.5312,
                0.54,
                false,
            ],
            [
                0.9,
                0.8,
                true,
            ],
            [
                0.0,
                0.0,
                true,
            ],
            [
                0.01,
                0.0,
                true,
            ],
            [
                1.0,
                1.0,
                true,
            ],
        ];
    }

    /**
     * @param float $calculatedSpamFactor
     * @param float $spamFactorLimit
     * @param bool $expectedResult
     * @return void
     * @dataProvider isSpamToleranceLimitReachedReturnsBoolDataProvider
     * @test
     * @covers ::isSpamToleranceLimitReached
     */
    public function isSpamToleranceLimitReachedReturnsBool($calculatedSpamFactor, $spamFactorLimit, $expectedResult)
    {
        $this->generalValidatorMock->_set('calculatedSpamFactor', $calculatedSpamFactor);
        $this->generalValidatorMock->_set('spamFactorLimit', $spamFactorLimit);
        self::assertSame($expectedResult, $this->generalValidatorMock->_callRef('isSpamToleranceLimitReached'));
    }
}
