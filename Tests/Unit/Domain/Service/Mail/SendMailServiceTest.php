<?php
namespace In2code\Powermail\Tests\Unit\Domain\Service\Mail;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class SendMailServiceTest
 *
 * Guards which values powermail hands to Fluid. Values that a website visitor can submit - the
 * sender of a mail to the receiver, the receiver of a mail to the sender - must never be used as a
 * Fluid template source.
 *
 * @coversDefaultClass \In2code\Powermail\Domain\Service\Mail\SendMailService
 */
class SendMailServiceTest extends UnitTestCase
{
    /**
     * @param string $type
     * @return \In2code\Powermail\Domain\Service\Mail\SendMailService
     */
    protected function getSubjectForType($type)
    {
        $subject = $this->getAccessibleMock(SendMailService::class, ['dummy'], [], '', false);
        $subject->_set('type', $type);
        return $subject;
    }

    /**
     * Dataprovider getKeysAllowedToContainFluidReturnsArray()
     *
     * @return array
     */
    public function getKeysAllowedToContainFluidReturnsArrayDataProvider()
    {
        return [
            'mail to the receiver: sender values come from the visitor' => [
                'receiver',
                ['receiverName', 'subject'],
            ],
            'disclaimer mail: sender values come from the visitor' => [
                'disclaimer',
                ['receiverName', 'subject'],
            ],
            'mail to the sender: receiver values come from the visitor' => [
                'sender',
                ['senderName', 'senderEmail', 'subject'],
            ],
            'optin mail: receiver values come from the visitor' => [
                'optin',
                ['senderName', 'senderEmail', 'subject'],
            ],
            'unknown mail type of another extension falls back to the subject only' => [
                'somethingCustom',
                ['subject'],
            ],
        ];
    }

    /**
     * @param string $type
     * @param array $expectedResult
     * @dataProvider getKeysAllowedToContainFluidReturnsArrayDataProvider
     * @return void
     * @test
     * @covers ::getKeysAllowedToContainFluid
     */
    public function getKeysAllowedToContainFluidReturnsArray($type, array $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * Dataprovider visitorValuesAreNeverParsed()
     *
     * @return array
     */
    public function visitorValuesAreNeverParsedDataProvider()
    {
        return [
            'sender name of a mail to the receiver' => ['receiver', 'senderName'],
            'sender email of a mail to the receiver' => ['receiver', 'senderEmail'],
            'sender name of a disclaimer mail' => ['disclaimer', 'senderName'],
            'sender email of a disclaimer mail' => ['disclaimer', 'senderEmail'],
            'receiver name of a mail to the sender' => ['sender', 'receiverName'],
            'receiver name of an optin mail' => ['optin', 'receiverName'],
        ];
    }

    /**
     * @param string $type
     * @param string $key
     * @dataProvider visitorValuesAreNeverParsedDataProvider
     * @return void
     * @test
     * @covers ::getKeysAllowedToContainFluid
     */
    public function visitorValuesAreNeverParsed($type, $key)
    {
        $this->assertNotContains(
            $key,
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * The receiver email is parsed once already, in
     * ReceiverMailReceiverPropertiesService::getEmailsFromFlexForm(), and by then it can contain
     * values that were substituted in from the submitted data.
     *
     * @param string $type
     * @dataProvider mailTypeDataProvider
     * @return void
     * @test
     * @covers ::getKeysAllowedToContainFluid
     */
    public function receiverEmailIsNeverParsedForAnyMailType($type)
    {
        $this->assertNotContains(
            'receiverEmail',
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * Dataprovider mailTypeDataProvider()
     *
     * @return array
     */
    public function mailTypeDataProvider()
    {
        return [
            'receiver' => ['receiver'],
            'sender' => ['sender'],
            'optin' => ['optin'],
            'disclaimer' => ['disclaimer'],
            'unknown type' => ['somethingCustom'],
        ];
    }
}
