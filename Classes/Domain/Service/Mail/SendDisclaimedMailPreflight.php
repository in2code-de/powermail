<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class SendDisclaimedMailPreflight
 * to notify the receiver about the disclaimed mail
 */
class SendDisclaimedMailPreflight
{
    protected SendMailService $sendMailService;

    protected MailRepository $mailRepository;

    protected Request $request;

    public function __construct(protected array $settings, protected array $conf, Request $request)
    {
        $this->sendMailService = GeneralUtility::makeInstance(SendMailService::class, $request);
        $this->mailRepository = GeneralUtility::makeInstance(MailRepository::class);
    }

    /**
     * @throws InvalidConfigurationTypeException
     * @throws ExceptionExtbaseObject
     */
    public function sendMail(Mail $mail): void
    {
        $receiverService = GeneralUtility::makeInstance(
            ReceiverMailReceiverPropertiesService::class,
            $mail,
            $this->settings
        );
        $senderService = GeneralUtility::makeInstance(
            ReceiverMailSenderPropertiesService::class,
            $mail,
            $this->settings
        );
        foreach ($receiverService->getReceiverEmails() as $receiver) {
            $email = [
                'template' => 'Mail/DisclaimedNotificationMail',
                'receiverEmail' => $receiver,
                'receiverName' => $receiverService->getReceiverName(),
                'senderEmail' => $senderService->getSenderEmail(),
                'senderName' => $senderService->getSenderName(),
                'replyToEmail' => $senderService->getSenderEmail(),
                'replyToName' => $senderService->getSenderName(),
                'subject' => ObjectUtility::getContentObject()->cObjGetSingle(
                    $this->conf['disclaimer.']['subject'],
                    $this->conf['disclaimer.']['subject.']
                ),
                'rteBody' => '',
                'format' => $this->settings['sender']['mailformat'],
                'variables' => ['mail' => $mail],
            ];
            $this->sendMailService->sendMail($email, $mail, $this->settings, 'disclaimer');
        }
    }
}
