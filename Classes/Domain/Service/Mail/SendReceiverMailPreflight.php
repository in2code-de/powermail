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
    protected SendMailService $sendMailService;

    protected Request $request;

    public function __construct(protected array $settings, Request $request)
    {
        $this->sendMailService = GeneralUtility::makeInstance(SendMailService::class, $request);
    }

    /**
     * @param string|null $hash
     * @throws InvalidConfigurationTypeException
     * @throws ExceptionExtbaseObject
     */
    public function sendReceiverMail(Mail $mail, ?string $hash = null): bool
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
        if (empty($this->settings['receiver']['subject'])) {
            // avoid error flashmessage if subject is deliberately empty (and thus deactivates mailing)
            return true;
        }

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
