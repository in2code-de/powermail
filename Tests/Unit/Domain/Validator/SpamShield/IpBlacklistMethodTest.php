<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class IpBlacklistMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod
 */
class IpBlacklistMethodTest extends UnitTestCase
{
    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp():void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            IpBlacklistMethod::class,
            ['dummy'],
            [
                new Mail(),
                [],
                []
            ]
        );
    }

    /**
     * @return void
     */
    public function tearDown():void
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
        $this->assertSame(',,,,,', $this->generalValidatorMock->_callRef('reduceDelimiters', $string));
    }
}
