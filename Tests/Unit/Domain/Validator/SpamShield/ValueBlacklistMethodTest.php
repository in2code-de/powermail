<?php

namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ValueBlacklistMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod
 */
class ValueBlacklistMethodTest extends UnitTestCase
{
    /**
     * @var ValueBlacklistMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            ValueBlacklistMethod::class,
            ['dummy'],
            [
                new Mail(),
                [],
                [],
            ]
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
     * @return void
     * @test
     * @covers ::reduceDelimiters
     */
    public function reduceDelimitersReturnsString()
    {
        $string = ',;,' . PHP_EOL . ',';
        self::assertSame(',,,,,', $this->generalValidatorMock->_call('reduceDelimiters', $string));
    }

    /**
     * Dataprovider findStringInStringReturnsString()
     *
     * @return array
     */
    public function findStringInStringReturnsStringDataProvider()
    {
        return [
            [
                'Sex',
                true,
            ],
            [
                'This sex was great',
                true,
            ],
            [
                'Staatsexamen',
                false,
            ],
            [
                '_sex_bla',
                true,
            ],
            [
                'tst sex.seems.to.be.nice',
                true,
            ],
            [
                'email@sex.org',
                true,
            ],
        ];
    }

    /**
     * @param string $string
     * @param bool $expectedResult
     * @return void
     * @dataProvider findStringInStringReturnsStringDataProvider
     * @test
     * @covers ::isStringInString
     */
    public function findStringInStringReturnsString($string, $expectedResult)
    {
        $needle = 'sex';
        self::assertSame(
            $expectedResult,
            $this->generalValidatorMock->_call('isStringInString', $string, $needle)
        );
    }
}
