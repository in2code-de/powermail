<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\LinkMethod;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class LinkMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\LinkMethod
 */
class LinkMethodTest extends UnitTestCase
{
    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\LinkMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            LinkMethod::class,
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
            'links allowed 1, 2 links given' => [
                '1',
                'xx <a href="http://www.test.de">http://www.test.de</a> xx',
                true
            ],
            'links allowed 3, 2 links given' => [
                '3',
                'xx <a href="ftp://www.test.de">https://www.test.de</a> xx',
                false
            ],
            'links allowed 0, 1 link given' => [
                '0',
                'xx <a href="#">https://www.test.de</a> xx',
                true
            ],
            'links allowed 2, 3 link given' => [
                '2',
                'xx [url=http://www.xyz.org]http://www.xyz.org[/url] http://www.xyz.org xx',
                true
            ],
        ];
    }

    /**
     * @param int $allowedLinks
     * @param string $text
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     * @covers ::spamCheck
     */
    public function spamCheckReturnsVoid($allowedLinks, $text, $expectedResult)
    {
        $mail = new Mail();
        $answer = new Answer();
        $answer->setValueType(0);
        $answer->setValue($text);
        $mail->addAnswer($answer);

        $this->generalValidatorMock->_set('mail', $mail);
        $this->generalValidatorMock->_set('configuration', ['linkLimit' => $allowedLinks]);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
