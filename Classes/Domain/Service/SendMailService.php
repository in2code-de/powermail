<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class SendMailService
 * @package In2code\Powermail\Domain\Service
 */
class SendMailService
{
    use SignalTrait;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     * @inject
     */
    protected $contentObject;

    /**
     * @var \In2code\Powermail\Domain\Service\PlaintextService
     * @inject
     */
    protected $plaintextService;

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
     * @param Mail &$mail
     * @param array $settings TypoScript Settings
     * @param string $type Email to "sender" or "receiver"
     * @return bool Mail successfully sent
     */
    public function sendMail(array $email, Mail &$mail, array $settings, $type = 'receiver')
    {
        $this->initialize($mail, $settings, $type);
        $this->parseAndOverwriteVariables($email, $mail);
        if ($settings['debug']['mail']) {
            GeneralUtility::devLog('Mail properties', 'powermail', 0, $email);
        }
        if (
            !GeneralUtility::validEmail($email['receiverEmail']) ||
            !GeneralUtility::validEmail($email['senderEmail'])
        ) {
            return false;
        }
        if (empty($email['subject'])) {
            // don't want an error flashmessage
            return true;
        }
        return $this->prepareAndSend($email);
    }

    /**
     * Prepare mail and send it
     *
     * @param array $email Array with all needed mail information
     * @return bool Mail successfully sent
     */
    protected function prepareAndSend(array $email)
    {
        /** @var MailMessage $message */
        $message = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $message
            ->setTo([$email['receiverEmail'] => $email['receiverName']])
            ->setFrom([$email['senderEmail'] => $email['senderName']])
            ->setReplyTo([$email['replyToEmail'] => $email['replyToName']])
            ->setSubject($email['subject'])
            ->setCharset(FrontendUtility::getCharset());
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

        $signalArguments = [$message, &$email, $this];
        $this->signalDispatch(__CLASS__, 'sendTemplateEmailBeforeSend', $signalArguments);

        $message->send();
        $this->updateMail($email);
        return $message->isSent();
    }

    /**
     * Add CC receivers
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    protected function addCc(MailMessage $message)
    {
        $ccValue = $this->contentObject->cObjGetSingle($this->overwriteConfig['cc'], $this->overwriteConfig['cc.']);
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
     */
    protected function addBcc(MailMessage $message)
    {
        $bccValue = $this->contentObject->cObjGetSingle($this->overwriteConfig['bcc'], $this->overwriteConfig['bcc.']);
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
     */
    protected function addReturnPath(MailMessage $message)
    {
        $returnPathValue = $this->contentObject->cObjGetSingle(
            $this->overwriteConfig['returnPath'],
            $this->overwriteConfig['returnPath.']
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
     */
    protected function addReplyAddresses(MailMessage $message)
    {
        $replyToEmail = $this->contentObject->cObjGetSingle(
            $this->overwriteConfig['replyToEmail'],
            $this->overwriteConfig['replyToEmail.']
        );
        $replyToName = $this->contentObject->cObjGetSingle(
            $this->overwriteConfig['replyToName'],
            $this->overwriteConfig['replyToName.']
        );
        if (!empty($replyToEmail) && !empty($replyToName)) {
            $message->setReplyTo(
                [
                    $replyToEmail => $replyToName
                ]
            );
        }
        return $message;
    }

    /**
     * Add mail priority
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    protected function addPriority(MailMessage $message)
    {
        $priorityValue = (int)$this->settings[$this->type]['overwrite']['priority'];
        if ($priorityValue > 0) {
            $message->setPriority($priorityValue);
        }
        return $message;
    }

    /**
     * Add attachments from upload fields
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    protected function addAttachmentsFromUploads(MailMessage $message)
    {
        if (!empty($this->settings[$this->type]['attachment']) && !empty($this->settings['misc']['file']['folder'])) {
            /** @var UploadService $uploadService */
            $uploadService = ObjectUtility::getObjectManager()->get(UploadService::class);
            foreach ($uploadService->getFiles() as $file) {
                if ($file->isUploaded() && $file->isValid() && $file->isFileExisting()) {
                    $message->attach(\Swift_Attachment::fromPath($file->getNewPathAndFilename(true)));
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
     */
    protected function addAttachmentsFromTypoScript(MailMessage $message)
    {
        $filesValue = $this->contentObject->cObjGetSingle(
            $this->configuration[$this->type . '.']['addAttachment'],
            $this->configuration[$this->type . '.']['addAttachment.']
        );
        if (!empty($filesValue)) {
            $files = GeneralUtility::trimExplode(',', $filesValue, true);
            foreach ($files as $file) {
                if (file_exists(GeneralUtility::getFileAbsFileName($file))) {
                    $message->attach(\Swift_Attachment::fromPath($file));
                } else {
                    GeneralUtility::devLog('Error: File to attach does not exist', 'powermail', 2, $file);
                }
            }
        }
        return $message;
    }

    /**
     * Add mail body html
     *
     * @param MailMessage $message
     * @param array $email
     * @return MailMessage
     */
    protected function addHtmlBody(MailMessage $message, array $email)
    {
        if ($email['format'] !== 'plain') {
            $message->setBody($this->createEmailBody($email), 'text/html');
        }
        return $message;
    }

    /**
     * Add mail body plain
     *
     * @param MailMessage $message
     * @param array $email
     * @return MailMessage
     */
    protected function addPlainBody(MailMessage $message, array $email)
    {
        if ($email['format'] !== 'html') {
            $message->addPart($this->plaintextService->makePlain($this->createEmailBody($email)), 'text/plain');
        }
        return $message;
    }

    /**
     * Set Sender Header according to RFC 2822 - 3.6.2 Originator fields
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    protected function addSenderHeader(MailMessage $message)
    {
        $senderHeaderConfig = $this->configuration[$this->type . '.']['senderHeader.'];
        $email = $this->contentObject->cObjGetSingle($senderHeaderConfig['email'], $senderHeaderConfig['email.']);
        $name = $this->contentObject->cObjGetSingle($senderHeaderConfig['name'], $senderHeaderConfig['name.']);
        if (GeneralUtility::validEmail($email)) {
            if (empty($name)) {
                $name = null;
            }
            $message->setSender($email, $name);
        }
        return $message;
    }

    /**
     * Create Email Body
     *
     * @param array $email Array with all needed mail information
     * @return bool
     */
    protected function createEmailBody($email)
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->getRequest()->setControllerName('Form');
        $standaloneView->setTemplatePathAndFilename(TemplateUtility::getTemplatePath($email['template'] . '.html'));

        // variables
        $variablesWithMarkers = $this->mailRepository->getVariablesWithMarkersFromMail($this->mail);
        $standaloneView->assignMultiple($variablesWithMarkers);
        $standaloneView->assignMultiple($this->mailRepository->getLabelsWithMarkersFromMail($this->mail));
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
     */
    protected function updateMail(array $email)
    {
        if ($this->type === 'receiver' && $email['variables']['hash'] === null) {
            $this->mail->setSenderMail($email['senderEmail']);
            $this->mail->setSenderName($email['senderName']);
            $this->mail->setSubject($email['subject']);
        }
    }

    /**
     * @param array $settings
     * @return array
     */
    protected function getConfigurationFromSettings(array $settings)
    {
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        return $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
    }

    /**
     * Parsing variables with fluid engine to allow viewhelpers in flexform
     *
     * @param array $email
     * @param Mail $mail
     * @return void
     */
    protected function parseAndOverwriteVariables(array &$email, Mail $mail)
    {
        TypoScriptUtility::overwriteValueFromTypoScript($email['subject'], $this->overwriteConfig, 'subject');
        TypoScriptUtility::overwriteValueFromTypoScript($email['senderName'], $this->overwriteConfig, 'senderName');
        TypoScriptUtility::overwriteValueFromTypoScript($email['senderEmail'], $this->overwriteConfig, 'senderEmail');
        TypoScriptUtility::overwriteValueFromTypoScript($email['receiverName'], $this->overwriteConfig, 'name');
        if ($this->type !== 'receiver') {
            // overwrite with TypoScript already done in ReceiverEmailService
            TypoScriptUtility::overwriteValueFromTypoScript($email['receiverEmail'], $this->overwriteConfig, 'email');
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
                $this->mailRepository->getVariablesWithMarkersFromMail($mail)
            );
        }
    }

    /**
     * @param Mail $mail
     * @param array $settings
     * @param string $type
     */
    protected function initialize(Mail &$mail, array $settings, $type)
    {
        $this->mail = &$mail;
        $this->settings = $settings;
        $this->configuration = $this->getConfigurationFromSettings($settings);
        $this->overwriteConfig = $this->configuration[$type . '.']['overwrite.'];
        $this->contentObject->start($this->mailRepository->getVariablesWithMarkersFromMail($mail));
        $this->type = $type;
    }

    /**
     * @return Mail $this->mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return string $this->type
     */
    public function getType()
    {
        return $this->type;
    }
}
