<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class SendReceiverMailPreflight
 */
class SendReceiverMailPreflight
{
    /**
     * @var SendMailService
     */
    protected SendMailService $sendMailService;

    /**
     * @var array
     */
    protected array $settings = [];

    protected Request $request;

    /**
     * @param array $settings
     */
    public function __construct(array $settings, Request $request)
    {
        $this->settings = $settings;
        $this->sendMailService = GeneralUtility::makeInstance(SendMailService::class, $request);
    }

    /**
     * @param Mail $mail
     * @param string|null $hash
     * @return bool
     * @throws InvalidConfigurationTypeException
     * @throws ExceptionExtbaseObject
     */
    public function sendReceiverMail(Mail $mail, string $hash = null): bool
    {
        $receiverService = GeneralUtility::makeInstance(
            ReceiverMailReceiverPropertiesService::class,
            $mail,
            $this->settings
        );
        $mail->setReceiverMail($receiverService->getReceiverEmailsString());
        $senderService = GeneralUtility::makeInstance(
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
                'variables' => [
                    'hash' => $hash,
                    'L' => FrontendUtility::getSysLanguageUid(),
                ],
            ];
            $isSent = $this->sendMailService->sendMail($email, $mail, $this->settings, 'receiver');
        }
        return $isSent;
    }
}
