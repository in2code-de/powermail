<?php
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\OptinUtility;

/**
 * Class SendOptinConfirmationMailPreflight
 */
class SendOptinConfirmationMailPreflight
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
     * SendOptinConfirmationMailPreflight constructor.
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
     * Send Optin Confirmation Mail to user
     *
     * @param Mail $mail
     * @return void
     */
    public function sendOptinConfirmationMail(Mail $mail)
    {
        $email = [
            'template' => $this->settings['main']['optin_template'] ?: 'Mail/OptinMail',
            'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
            'receiverName' => $this->mailRepository->getSenderNameFromArguments(
                $mail,
                [$this->conf['sender.']['default.'], 'senderName']
            ),
            'senderEmail' => $this->settings['sender']['email'],
            'senderName' => $this->settings['sender']['name'],
            'replyToEmail' => $this->settings['sender']['email'],
            'replyToName' => $this->settings['sender']['name'],
            'subject' => $this->settings['main']['optin_subject'] ?: $this->getDefaultSubject(),
            'rteBody' => '',
            'format' => $this->settings['sender']['mailformat'],
            'variables' => [
                'hash' => OptinUtility::createOptinHash($mail),
                'mail' => $mail
            ]
        ];
        $this->sendMailService->sendMail($email, $mail, $this->settings, 'optin');
    }

    /**
     * @return string
     */
    private function getDefaultSubject()
    {
        return ObjectUtility::getContentObject()->cObjGetSingle(
            $this->conf['optin.']['subject'],
            $this->conf['optin.']['subject.']
        );
    }
}
