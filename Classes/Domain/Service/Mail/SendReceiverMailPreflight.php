<?php
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class SendReceiverMailPreflight
 */
class SendReceiverMailPreflight
{

    /**
     * @var \In2code\Powermail\Domain\Service\Mail\SendMailService
     */
    protected $sendMailService;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * SendReceiverMailService constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->sendMailService = ObjectUtility::getObjectManager()->get(SendMailService::class);
    }

    /**
     * Mail Generation for Receiver
     *
     * @param Mail $mail
     * @param string $hash
     * @return bool
     */
    public function sendReceiverMail(Mail $mail, $hash = null)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $receiverService = ObjectUtility::getObjectManager()->get(
            ReceiverMailReceiverPropertiesService::class,
            $mail,
            $this->settings
        );
        $mail->setReceiverMail($receiverService->getReceiverEmailsString());
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $senderService = ObjectUtility::getObjectManager()->get(
            ReceiverMailSenderPropertiesService::class,
            $mail,
            $this->settings
        );
        $isSent = false;
        foreach ($receiverService->getReceiverEmails() as $receiver) {
            $email = [
                'template' => 'Mail/ReceiverMail',
                'receiverEmail' => $receiver,
                'receiverName' => $receiverService->getReceiverName(),
                'senderEmail' => $senderService->getSenderEmail(),
                'senderName' => $senderService->getSenderName(),
                'replyToEmail' => $senderService->getSenderEmail(),
                'replyToName' => $senderService->getSenderName(),
                'subject' => $this->settings['receiver']['subject'],
                'rteBody' => $this->settings['receiver']['body'],
                'format' => $this->settings['receiver']['mailformat'],
                'variables' => ['hash' => $hash]
            ];
            $isSent = $this->sendMailService->sendMail($email, $mail, $this->settings, 'receiver');
        }
        return $isSent;
    }
}
