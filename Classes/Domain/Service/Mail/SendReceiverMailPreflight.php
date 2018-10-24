<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidControllerNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SendReceiverMailPreflight
 */
class SendReceiverMailPreflight
{

    /**
     * @var SendMailService
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
     * @param Mail $mail
     * @param null $hash
     * @return bool
     * @throws InvalidConfigurationTypeException
     * @throws InvalidControllerNameException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
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
                'variables' => [
                    'hash' => $hash,
                    'L' => FrontendUtility::getSysLanguageUid()
                ]
            ];
            $isSent = $this->sendMailService->sendMail($email, $mail, $this->settings, 'receiver');
        }
        return $isSent;
    }
}
