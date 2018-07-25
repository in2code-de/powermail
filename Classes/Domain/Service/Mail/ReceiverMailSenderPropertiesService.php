<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class ReceiverMailSenderPropertiesService to get email array for sender attributes
 */
class ReceiverMailSenderPropertiesService
{
    use SignalTrait;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     */
    protected $mailRepository;

    /**
     * @var Mail|null
     */
    protected $mail = null;

    /**
     * TypoScript settings as plain array
     *
     * @var array
     */
    protected $settings = [];

    /**
     * TypoScript configuration for cObject parsing
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * @param Mail $mail
     * @param array $settings
     */
    public function __construct(Mail $mail, array $settings)
    {
        $this->mail = $mail;
        $this->settings = $settings;
        $this->mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * Get sender email from configuration in fields and params. If empty, take default from TypoScript
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getSenderEmail()
    {
        TypoScriptUtility::overwriteValueFromTypoScript(
            $defaultSenderEmail,
            $this->configuration['receiver.']['default.'],
            'senderEmail'
        );
        $senderEmail = $this->mailRepository->getSenderMailFromArguments($this->mail, $defaultSenderEmail);

        $signalArguments = [&$senderEmail, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderEmail;
    }

    /**
     * Get sender name from configuration in fields and params. If empty, take default from TypoScript
     *
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getSenderName()
    {
        TypoScriptUtility::overwriteValueFromTypoScript(
            $defaultSenderName,
            $this->configuration['receiver.']['default.'],
            'senderName'
        );
        $senderName = $this->mailRepository->getSenderNameFromArguments($this->mail, $defaultSenderName);

        $signalArguments = [&$senderName, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderName;
    }
}
