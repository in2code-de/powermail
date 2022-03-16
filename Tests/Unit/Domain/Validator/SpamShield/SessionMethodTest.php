<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\SpamShield;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod;
use In2code\Powermail\Domain\Validator\SpamShield\SessionMethod;
use In2code\Powermail\Utility\SessionUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class SessionMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\SessionMethod
 */
class SessionMethodTest extends UnitTestCase
{
    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\SessionMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            SessionMethod::class,
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
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * @return void
     * @test
     * @covers ::spamCheck
     */
    public function spamCheckReturnsVoid()
    {
        $settings = [
            'spamshield' => [
                'methods' => [
                    [
                        'class' => HoneyPodMethod::class,
                        '_enable' => '1'
                    ],
                ],
                '_enable' => '1'
            ]
        ];
        $form = new Form();
        $form->_setProperty('uid', 123);
        SessionUtility::saveFormStartInSession($settings, $form);

        $mail = new Mail();
        $mail->setForm($form);

        $this->generalValidatorMock->_set('mail', $mail);
        $this->assertSame(true, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
