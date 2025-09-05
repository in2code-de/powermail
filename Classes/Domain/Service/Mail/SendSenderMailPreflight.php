<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\HashUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class SendSenderMailPreflight
 */
class SendSenderMailPreflight
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
    public function sendSenderMail(Mail $mail): void
    {
        $senderService = GeneralUtility::makeInstance(SenderMailPropertiesService::class, $this->settings);
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
            'format' => $this->settings['sender']['mailformat'],
            'variables' => [
                'hashDisclaimer' => HashUtility::getHash($mail, 'disclaimer'),
                'L' => FrontendUtility::getSysLanguageUid(),
            ],
        ];
        $this->sendMailService->sendMail($email, $mail, $this->settings, 'sender');
    }
}
