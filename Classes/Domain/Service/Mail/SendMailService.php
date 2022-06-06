<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility as ArrayUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class SendMailService
 */
class SendMailService
{
    use SignalTrait;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $overwriteConfig;

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var string
     */
    protected $type = 'receiver';

    /**
     * This is the main-function for sending Mails
     *
     * @param array $email Array with all needed mail information
     *        $email['receiverName'] = 'Name';
     *        $email['receiverEmail'] = 'receiver@mail.com';
     *        $email['senderName'] = 'Name';
     *        $email['senderEmail'] = 'sender@mail.com';
     *        $email['replyToName'] = 'Name';
     *        $email['replyToEmail'] = 'sender@mail.com';
     *        $email['subject'] = 'Subject line';
     *        $email['template'] = 'PathToTemplate/';
     *        $email['rteBody'] = 'This is the <b>content</b> of the RTE';
     *        $email['format'] = 'both'; // or plain or html
     * @param Mail $mail
     * @param array $settings TypoScript Settings
     * @param string $type Email to "sender" or "receiver"
     * @return bool Mail successfully sent
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function sendMail(array $email, Mail $mail, array $settings, string $type = 'receiver'): bool
    {
        $this->initialize($mail, $settings, $type);
        $this->parseAndOverwriteVariables($email, $mail);
        if ($settings['debug']['mail']) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->info('Mail properties', [$email]);
        }
        if (!GeneralUtility::validEmail($email['receiverEmail']) ||
            !GeneralUtility::validEmail($email['senderEmail'])) {
            return false;
        }
        if (empty($email['subject'])) {
            // don't want an error flashmessage
            return true;
        }
        return $this->prepareAndSend($email);
    }

    /**
     * @param array $email
     * @return bool
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function prepareAndSend(array $email): bool
    {
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message
            ->setTo([$email['receiverEmail'] => $email['receiverName']])
            ->setFrom([$email['senderEmail'] => $email['senderName']])
            ->setReplyTo([$email['replyToEmail'] => $email['replyToName']])
            ->setSubject($email['subject']);
        $message = $this->addCc($message);
        $message = $this->addBcc($message);
        $message = $this->addReturnPath($message);
        $message = $this->addReplyAddresses($message);
        $message = $this->addPriority($message);
        $message = $this->addAttachmentsFromUploads($message);
        $message = $this->addAttachmentsFromTypoScript($message);
        $message = $this->addHtmlBody($message, $email);
        $message = $this->addPlainBody($message, $email);
        $message = $this->addSenderHeader($message);

        $email['send'] = true;
        $signalArguments = [$message, &$email, $this];
        $this->signalDispatch(__CLASS__, 'sendTemplateEmailBeforeSend', $signalArguments);
        if (!$email['send']) {
            if ($this->settings['debug']['mail']) {
                $logger = ObjectUtility::getLogger(__CLASS__);
                $logger->info(
                    'Mail was not sent: the signal has aborted sending. Email array after signal execution:',
                    [$email]
                );
            }
            return false;
        }

        $message->send();
        $this->updateMail($email);
        return $message->isSent();
    }

    /**
     * Add CC receivers
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addCc(MailMessage $message): MailMessage
    {
        $ccValue = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->overwriteConfig['cc'] ?? '',
            $this->overwriteConfig['cc.'] ?? []
        );
        if (!empty($ccValue)) {
            $message->setCc(GeneralUtility::trimExplode(',', $ccValue, true));
        }
        return $message;
    }

    /**
     * Add BCC receivers
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addBcc(MailMessage $message): MailMessage
    {
        $bccValue = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->overwriteConfig['bcc'] ?? '',
            $this->overwriteConfig['bcc.'] ?? []
        );
        if (!empty($bccValue)) {
            $message->setBcc(GeneralUtility::trimExplode(',', $bccValue, true));
        }
        return $message;
    }

    /**
     * Add return path
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addReturnPath(MailMessage $message): MailMessage
    {
        $returnPathValue = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->overwriteConfig['returnPath'] ?? '',
            $this->overwriteConfig['returnPath.'] ?? []
        );
        if (!empty($returnPathValue)) {
            $message->setReturnPath($returnPathValue);
        }
        return $message;
    }

    /**
     * Add reply addresses if replyToEmail and replyToName isset
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addReplyAddresses(MailMessage $message): MailMessage
    {
        $replyToEmail = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->overwriteConfig['replyToEmail']??'',
            $this->overwriteConfig['replyToEmail.']??[]
        );
        $replyToName = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->overwriteConfig['replyToName']??'',
            $this->overwriteConfig['replyToName.']??[]
        );
        if (!empty($replyToEmail) && !empty($replyToName)) {
            $message->setReplyTo([$replyToEmail => $replyToName]);
        }
        return $message;
    }

    /**
     * Add mail priority
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    protected function addPriority(MailMessage $message): MailMessage
    {
        $message->priority(1);
        if ($this->type === 'sender') {
            $this->settings['sender']['overwrite'] = $this->settings['sender']['overwrite']??[];
            $message->priority((int)($this->settings['sender']['overwrite']['priority']??0));
        } elseif ($this->type === 'receiver') {
            $this->settings['receiver']['overwrite'] = $this->settings['receiver']['overwrite']??[];
            $message->priority((int)($this->settings['receiver']['overwrite']['priority']??0));
        }
        return $message;
    }

    /**
     * @param MailMessage $message
     * @return MailMessage
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function addAttachmentsFromUploads(MailMessage $message): MailMessage
    {
        if (!empty($this->settings[$this->type]['attachment']) && !empty($this->settings['misc']['file']['folder'])) {
            /** @var UploadService $uploadService */
            $uploadService = GeneralUtility::makeInstance(UploadService::class);
            foreach ($uploadService->getFiles() as $file) {
                if ($file->isUploaded() && $file->isValid() && $file->isFileExisting()) {
                    $message->attachFromPath($file->getNewPathAndFilename(true));
                }
            }
        }
        return $message;
    }

    /**
     * Add attachments from TypoScript definition
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addAttachmentsFromTypoScript(MailMessage $message): MailMessage
    {
        $filesValue = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->configuration[$this->type . '.']['addAttachment']??'',
            $this->configuration[$this->type . '.']['addAttachment.']??[]
        );
        if (!empty($filesValue)) {
            $files = GeneralUtility::trimExplode(',', $filesValue, true);
            foreach ($files as $file) {
                $fileAbsolute = GeneralUtility::getFileAbsFileName($file);
                if (file_exists($fileAbsolute)) {
                    $message->attachFromPath($fileAbsolute);
                } else {
                    $logger = ObjectUtility::getLogger(__CLASS__);
                    $logger->critical('File to attach does not exist', [$file]);
                }
            }
        }
        return $message;
    }

    /**
     * @param MailMessage $message
     * @param array $email
     * @return MailMessage
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function addHtmlBody(MailMessage $message, array $email): MailMessage
    {
        if ($email['format'] !== 'plain') {
            $message->html($this->createEmailBody($email), FrontendUtility::getCharset());
        }
        return $message;
    }

    /**
     * @param MailMessage $message
     * @param array $email
     * @return MailMessage
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function addPlainBody(MailMessage $message, array $email): MailMessage
    {
        if ($email['format'] !== 'html') {
            $plaintextService = GeneralUtility::makeInstance(PlaintextService::class);
            $message->text($plaintextService->makePlain($this->createEmailBody($email)), FrontendUtility::getCharset());
        }
        return $message;
    }

    /**
     * Set Sender Header according to RFC 2822 - 3.6.2 Originator fields
     *
     * @param MailMessage $message
     * @return MailMessage
     * @throws Exception
     */
    protected function addSenderHeader(MailMessage $message): MailMessage
    {
        if ($this->type === 'sender') {
            $senderHeaderConfig= $this->configuration['sender.']['senderHeader.']??[];
        } elseif ($this->type === 'receiver') {
            $senderHeaderConfig = $this->settings['receiver.']['senderHeader.']??[];
        }

        $email = ObjectUtility::getContentObject()->cObjGetSingle(
            $senderHeaderConfig['email']??'',
            $senderHeaderConfig['email.']??[]
        );
        $name = ObjectUtility::getContentObject()->cObjGetSingle(
            $senderHeaderConfig['name']??'',
            $senderHeaderConfig['name.']??[]
        );
        if (GeneralUtility::validEmail($email)) {
            if (empty($name)) {
                $name = null;
            }
            $message->setSender($email, $name);
        }
        return $message;
    }

    /**
     * @param array $email
     * @return string
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws Exception
     */
    protected function createEmailBody(array $email): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->getRequest()->setControllerName('Form');
        $standaloneView->setTemplatePathAndFilename(TemplateUtility::getTemplatePath($email['template'] . '.html'));

        // variables
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $variablesWithMarkers = $mailRepository->getVariablesWithMarkersFromMail($this->mail);
        $standaloneView->assignMultiple($variablesWithMarkers);
        $standaloneView->assignMultiple($mailRepository->getLabelsWithMarkersFromMail($this->mail));
        $standaloneView->assignMultiple(
            [
                'variablesWithMarkers' => ArrayUtility::htmlspecialcharsOnArray($variablesWithMarkers),
                'powermail_all' => TemplateUtility::powermailAll($this->mail, 'mail', $this->settings, $this->type),
                'powermail_rte' => $email['rteBody'],
                'marketingInfos' => SessionUtility::getMarketingInfos(),
                'mail' => $this->mail,
                'email' => $email,
                'settings' => $this->settings
            ]
        );
        if (!empty($email['variables'])) {
            $standaloneView->assignMultiple($email['variables']);
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRender', [$standaloneView, $email, $this]);
        $body = $standaloneView->render();
        $this->mail->setBody($body);
        return $body;
    }

    /**
     * Update mail record with parsed fields
     *
     * @param array $email
     * @return void
     */
    protected function updateMail(array $email): void
    {
        if ($this->type === 'receiver' && $email['variables']['hash'] === '') {
            $this->mail->setSenderMail($email['senderEmail']);
            $this->mail->setSenderName($email['senderName']);
            $this->mail->setReceiverMail($email['receiverEmail']);
            $this->mail->setSubject($email['subject']);
        }
    }

    /**
     * @param array $settings
     * @return array
     */
    protected function getConfigurationFromSettings(array $settings): array
    {
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        return $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
    }

    /**
     * Parsing variables with fluid engine to allow viewhelpers in flexform
     *
     * @param array $email
     * @param Mail $mail
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function parseAndOverwriteVariables(array &$email, Mail $mail): void
    {
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $email['subject'] = TypoScriptUtility::overwriteValueFromTypoScript(
            $email['subject'],
            $this->overwriteConfig,
            'subject'
        );
        $email['senderName'] = TypoScriptUtility::overwriteValueFromTypoScript(
            $email['senderName'],
            $this->overwriteConfig,
            'senderName'
        );
        $email['senderEmail'] = TypoScriptUtility::overwriteValueFromTypoScript(
            $email['senderEmail'],
            $this->overwriteConfig,
            'senderEmail'
        );
        $email['receiverName'] = TypoScriptUtility::overwriteValueFromTypoScript(
            $email['receiverName'],
            $this->overwriteConfig,
            'name'
        );
        if ($this->type !== 'receiver') {
            // overwrite with TypoScript already done in ReceiverMailReceiverPropertiesService
            $email['receiverEmail'] = TypoScriptUtility::overwriteValueFromTypoScript(
                $email['receiverEmail'],
                $this->overwriteConfig,
                'email'
            );
        }
        $parse = [
            'receiverName',
            'receiverEmail',
            'senderName',
            'senderEmail',
            'subject'
        ];
        foreach ($parse as $value) {
            $email[$value] = TemplateUtility::fluidParseString(
                $email[$value],
                $mailRepository->getVariablesWithMarkersFromMail($mail)
            );
        }
    }

    /**
     * @param Mail $mail
     * @param array $settings
     * @param string $type
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function initialize(Mail $mail, array $settings, string $type): void
    {
        $this->mail = $mail;
        $this->settings = $settings;
        $this->configuration = $this->getConfigurationFromSettings($settings);
        if (ArrayUtilityCore::isValidPath($this->configuration, $type . './overwrite.')) {
            $this->overwriteConfig = $this->configuration[$type . '.']['overwrite.'];
        }
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        ObjectUtility::getContentObject()->start($mailRepository->getVariablesWithMarkersFromMail($mail));
        $this->type = $type;
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getOverwriteConfig(): array
    {
        return $this->overwriteConfig;
    }
}
