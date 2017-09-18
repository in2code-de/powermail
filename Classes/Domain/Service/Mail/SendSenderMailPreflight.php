<?php
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class SendSenderMailPreflight
 */
class SendSenderMailPreflight
{

    /**
     * @var \In2code\Powermail\Domain\Service\Mail\SendMailService
     */
    protected $sendMailService;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     */
    protected $mailRepository;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $conf = [];

    /**
     * SendSenderMailPreflight constructor.
     *
     * @param array $settings
     * @param array $conf
     */
    public function __construct(array $settings, array $conf)
    {
        $this->settings = $settings;
        $this->conf = $conf;
        $this->sendMailService = ObjectUtility::getObjectManager()->get(SendMailService::class);
        $this->mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
    }

    /**
     * Mail Generation for Sender
     *
     * @param Mail $mail
     * @return void
     */
    public function sendSenderMail(Mail $mail)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $senderService = ObjectUtility::getObjectManager()->get(SenderMailPropertiesService::class, $this->settings);
        $email = [
            'template' => 'Mail/SenderMail',
            'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
            'receiverName' => $this->mailRepository->getSenderNameFromArguments(
                $mail,
                [$this->conf['sender.']['default.'], 'senderName']
            ),
            'senderEmail' => $senderService->getSenderEmail(),
            'senderName' => $senderService->getSenderName(),
            'replyToEmail' => $senderService->getSenderEmail(),
            'replyToName' => $senderService->getSenderName(),
            'subject' => $this->settings['sender']['subject'],
            'rteBody' => $this->settings['sender']['body'],
            'format' => $this->settings['sender']['mailformat']
        ];
        $this->sendMailService->sendMail($email, $mail, $this->settings, 'sender');
    }
}
