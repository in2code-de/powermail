<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class HoneyPodMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod
 */
class HoneyPodMethodTest extends UnitTestCase
{
    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            HoneyPodMethod::class,
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
     * Dataprovider spamCheckReturnsVoid()
     *
     * @return array
     */
    public function spamCheckReturnsVoidDataProvider()
    {
        return [
            'pot filled 1' => [
                'abc',
                true
            ],
            'pot filled 2' => [
                '@test',
                true
            ],
            'pot empty' => [
                '',
                false
            ],
        ];
    }

    /**
     * @param string $pot if $piVars['field']['__hp'] filled
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     * @covers ::spamCheck
     */
    public function spamCheckReturnsVoid($pot, $expectedResult)
    {
        $this->generalValidatorMock->_set('arguments', ['field' => ['__hp' => $pot]]);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
