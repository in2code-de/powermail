<?php

namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class UniqueMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod
 */
class UniqueMethodTest extends UnitTestCase
{
    /**
     * @var UniqueMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            UniqueMethod::class,
            null,
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
     * Dataprovider spamCheckReturnsVoid()
     *
     * @return array
     */
    public static function spamCheckReturnsVoidDataProvider(): array
    {
        return [
            [
                [
                    'abcdef',
                    'abcdef',
                    '123',
                    '123',
                ],
                true,
            ],
            [
                [
                    'alexander',
                    'kellner',
                    'alexander.kellner@test.org',
                    'This is an example text',
                    [
                        'abc',
                        'def',
                    ],
                ],
                false,
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
        self::assertSame($expectedResult, $this->generalValidatorMock->_call('spamCheck'));
    }
}
