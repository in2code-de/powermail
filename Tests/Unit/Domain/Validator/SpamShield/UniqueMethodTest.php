<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Exception;

/**
 * Class UniqueMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod
 */
class UniqueMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod
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
            UniqueMethod::class,
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
     * @param array $answerProperties
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     * @covers ::spamCheck
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
