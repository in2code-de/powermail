<?php

declare(strict_types=1);
namespace In2code\Powermail\Tests\Unit\Domain\Service\Mail;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class SendMailServiceTest
 *
 * Guards which values powermail hands to Fluid. Values that a website visitor can submit - the sender
 * of a mail to the receiver, the receiver of a mail to the sender - must never be used as a Fluid
 * template source.
 *
 * Note that powermail 10 - unlike later major versions - never parsed replyToName and replyToEmail,
 * so those two never show up in the expected results below.
 *
 * @covers \In2code\Powermail\Domain\Service\Mail\SendMailService
 */
class SendMailServiceTest extends UnitTestCase
{
    /**
     * @param string $type
     * @return mixed
     */
    protected function getSubjectForType(string $type)
    {
        $subject = $this->getAccessibleMock(SendMailService::class, ['dummy'], [], '', false);
        $subject->_set('type', $type);
        return $subject;
    }

    /**
     * Data Provider for getKeysAllowedToContainFluidReturnsArray()
     *
     * @return \Iterator
     */
    public function getKeysAllowedToContainFluidReturnsArrayDataProvider(): \Iterator
    {
        yield 'mail to the receiver: sender values come from the visitor' => [
            'receiver',
            ['receiverName', 'subject'],
        ];
        yield 'disclaimer mail: sender values come from the visitor' => [
            'disclaimer',
            ['receiverName', 'subject'],
        ];
        yield 'mail to the sender: receiver values come from the visitor' => [
            'sender',
            ['senderName', 'senderEmail', 'subject'],
        ];
        yield 'optin mail: receiver values come from the visitor' => [
            'optin',
            ['senderName', 'senderEmail', 'subject'],
        ];
        yield 'unknown mail type of another extension falls back to the subject only' => [
            'somethingCustom',
            ['subject'],
        ];
    }

    /**
     * @param string $type
     * @param array $expectedResult
     * @dataProvider getKeysAllowedToContainFluidReturnsArrayDataProvider
     * @return void
     * @test
     */
    public function getKeysAllowedToContainFluidReturnsArray($type, $expectedResult)
    {
        self::assertSame(
            $expectedResult,
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * Data Provider for visitorValuesAreNeverParsed()
     *
     * @return \Iterator
     */
    public function visitorValuesAreNeverParsedDataProvider(): \Iterator
    {
        yield 'sender name of a mail to the receiver' => ['receiver', 'senderName'];
        yield 'sender email of a mail to the receiver' => ['receiver', 'senderEmail'];
        yield 'sender name of a disclaimer mail' => ['disclaimer', 'senderName'];
        yield 'sender email of a disclaimer mail' => ['disclaimer', 'senderEmail'];
        yield 'receiver name of a mail to the sender' => ['sender', 'receiverName'];
        yield 'receiver name of an optin mail' => ['optin', 'receiverName'];
    }

    /**
     * @param string $type
     * @param string $key
     * @dataProvider visitorValuesAreNeverParsedDataProvider
     * @return void
     * @test
     */
    public function visitorValuesAreNeverParsed($type, $key)
    {
        self::assertNotContains(
            $key,
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * Data Provider for the mail types
     *
     * @return \Iterator
     */
    public function mailTypeDataProvider(): \Iterator
    {
        yield 'receiver' => ['receiver'];
        yield 'sender' => ['sender'];
        yield 'optin' => ['optin'];
        yield 'disclaimer' => ['disclaimer'];
        yield 'unknown type' => ['somethingCustom'];
    }

    /**
     * The receiver email is parsed once already, in
     * ReceiverMailReceiverPropertiesService::getEmailsFromFlexForm(), and by then it can contain
     * values that were substituted in from the submitted data. GeneralUtility::validEmail() accepts a
     * quoted local part, so being a valid address is no proof that a value is harmless.
     *
     * @param string $type
     * @dataProvider mailTypeDataProvider
     * @return void
     * @test
     */
    public function receiverEmailIsNeverParsedForAnyMailType($type)
    {
        self::assertNotContains(
            'receiverEmail',
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * A subject like "Message from {firstname}" is a documented feature
     *
     * @param string $type
     * @dataProvider mailTypeDataProvider
     * @return void
     * @test
     */
    public function subjectIsAlwaysParsed($type)
    {
        self::assertContains(
            'subject',
            $this->getSubjectForType($type)->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * The receiver name of a mail to the receiver comes from FlexForm, where Fluid is documented
     *
     * @return void
     * @test
     */
    public function configuredReceiverNameIsParsedForAMailToTheReceiver()
    {
        self::assertContains(
            'receiverName',
            $this->getSubjectForType('receiver')->_call('getKeysAllowedToContainFluid')
        );
    }

    /**
     * The sender name of a mail to the sender comes from TypoScript or FlexForm
     *
     * @return void
     * @test
     */
    public function configuredSenderNameIsParsedForAMailToTheSender()
    {
        self::assertContains(
            'senderName',
            $this->getSubjectForType('sender')->_call('getKeysAllowedToContainFluid')
        );
    }
}
