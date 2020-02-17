<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Exception;

/**
 * Class ValueBlacklistMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod
 */
class ValueBlacklistMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod
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
            ValueBlacklistMethod::class,
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
                true
            ],
            [
                'This sex was great',
                true
            ],
            [
                'Staatsexamen',
                false
            ],
            [
                '_sex_bla',
                true
            ],
            [
                'tst sex.seems.to.be.nice',
                true
            ],
            [
                'email@sex.org',
                true
            ]
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
        $this->assertSame(
            $expectedResult,
            $this->generalValidatorMock->_callRef('isStringInString', $string, $needle)
        );
    }
}
