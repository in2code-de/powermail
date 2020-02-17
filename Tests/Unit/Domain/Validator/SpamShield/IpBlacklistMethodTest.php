<?php
namespace In2code\Powermail\Tests\Domain\Unit\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Exception;

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
     * @throws Exception
     */
    public function setUp()
    {
        TestingHelper::initializeTypoScriptFrontendController();
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
    public function tearDown()
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
