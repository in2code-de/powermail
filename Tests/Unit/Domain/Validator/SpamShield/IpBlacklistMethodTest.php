<?php

namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class IpBlacklistMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod
 */
class IpBlacklistMethodTest extends UnitTestCase
{
    /**
     * @var IpBlacklistMethod
     */
    protected $generalValidatorMock;

    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            IpBlacklistMethod::class,
            null,
            [
                new Mail(),
                [],
                [],
            ]
        );
    }

    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * @test
     * @covers ::reduceDelimiters
     */
    public function reduceDelimitersReturnsString(): void
    {
        $string = ',;,' . PHP_EOL . ',';
        self::assertSame(',,,,,', $this->generalValidatorMock->_call('reduceDelimiters', $string));
    }
}
