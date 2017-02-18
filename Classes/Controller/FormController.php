<?php
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ReceiverEmailService;
use In2code\Powermail\Domain\Service\SenderEmailService;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\OptinUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Controller for powermail forms
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FormController extends AbstractController
{

    /**
     * @var \In2code\Powermail\Domain\Service\SendMailService
     * @inject
     */
    protected $sendMailService;

    /**
     * @var \In2code\Powermail\Finisher\FinisherRunner
     * @inject
     */
    protected $finisherRunner;

    /**
     * @var \In2code\Powermail\DataProcessor\DataProcessorRunner
     * @inject
     */
    protected $dataProcessorRunner;

    /**
     * action show form for creating new mails
     *
     * @return void
     */
    public function formAction()
    {
        $form = $this->formRepository->findByUid($this->settings['main']['form']);
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$form, $this]);
        SessionUtility::saveFormStartInSession($this->settings, $form);
        $this->view->assignMultiple(
            [
                'form' => $form,
                'ttContentData' => $this->contentObject->data,
                'messageClass' => $this->messageClass,
                'action' => ($this->settings['main']['confirmation'] ? 'confirmation' : 'create')
            ]
        );
    }

    /**
     * Rewrite Arguments to receive a clean mail object in createAction
     *
     * @return void
     */
    public function initializeCreateAction()
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamEmpty();
        $this->reformatParamsForAction();
        $this->debugVariables();
    }

    /**
     * Action create entry
     *
     * @param Mail $mail
     * @param string $hash
     * @validate $mail In2code\Powermail\Domain\Validator\UploadValidator
     * @validate $mail In2code\Powermail\Domain\Validator\InputValidator
     * @validate $mail In2code\Powermail\Domain\Validator\PasswordValidator
     * @validate $mail In2code\Powermail\Domain\Validator\CaptchaValidator
     * @validate $mail In2code\Powermail\Domain\Validator\SpamShieldValidator
     * @validate $mail In2code\Powermail\Domain\Validator\UniqueValidator
     * @validate $mail In2code\Powermail\Domain\Validator\ForeignValidator
     * @validate $mail In2code\Powermail\Domain\Validator\CustomValidator
     * @required $mail
     * @return void
     */
    public function createAction(Mail $mail, $hash = null)
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $hash, $this]);
        $this->dataProcessorRunner->callDataProcessors(
            $mail,
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );
        if ($this->isMailPersistActive($hash)) {
            $this->saveMail($mail);
            $this->signalDispatch(__CLASS__, __FUNCTION__ . 'AfterMailDbSaved', [$mail, $this]);
        }
        if ($this->isSendMailActive($mail, $hash)) {
            $this->sendMailPreflight($mail, $hash);
        } else {
            $this->sendConfirmationMail($mail);
            $this->view->assign('optinActive', true);
        }
        if ($this->settings['db']['enable']) {
            $this->mailRepository->update($mail);
            $this->persistenceManager->persistAll();
        }

        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'AfterSubmitView', [$mail, $hash, $this]);
        $this->prepareOutput($mail);

        $this->finisherRunner->callFinishers(
            $mail,
            $this->isSendMailActive($mail, $hash),
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );
    }

    /**
     * Rewrite Arguments to receive a clean mail object in confirmationAction
     *
     * @return void
     */
    public function initializeConfirmationAction()
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamEmpty();
        $this->reformatParamsForAction();
        $this->debugVariables();
    }

    /**
     * Show Confirmation message after submit (if view is activated)
     *
     * @param Mail $mail
     * @validate $mail In2code\Powermail\Domain\Validator\UploadValidator
     * @validate $mail In2code\Powermail\Domain\Validator\InputValidator
     * @validate $mail In2code\Powermail\Domain\Validator\PasswordValidator
     * @validate $mail In2code\Powermail\Domain\Validator\CaptchaValidator
     * @validate $mail In2code\Powermail\Domain\Validator\SpamShieldValidator
     * @validate $mail In2code\Powermail\Domain\Validator\UniqueValidator
     * @validate $mail In2code\Powermail\Domain\Validator\ForeignValidator
     * @validate $mail In2code\Powermail\Domain\Validator\CustomValidator
     * @required $mail
     * @return void
     */
    public function confirmationAction(Mail $mail)
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $this]);
        $this->dataProcessorRunner->callDataProcessors(
            $mail,
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );
        $this->prepareOutput($mail);
    }

    /**
     * @param Mail $mail
     * @param string $hash
     * @return void
     */
    protected function sendMailPreflight(Mail $mail, $hash = null)
    {
        try {
            if ($this->settings['sender']['enable'] && $this->mailRepository->getSenderMailFromArguments($mail)) {
                $this->sendSenderMail($mail);
            }
            if ($this->settings['receiver']['enable']) {
                $this->sendReceiverMail($mail, $hash);
            }
        } catch (\Exception $exception) {
            GeneralUtility::sysLog($exception->getMessage(), 'powermail', GeneralUtility::SYSLOG_SEVERITY_WARNING);
            $this->addFlashMessage(LocalizationUtility::translate('mail_created_failure'), '', AbstractMessage::ERROR);
        }
    }

    /**
     * Mail Generation for Receiver
     *
     * @param Mail $mail
     * @param string $hash
     * @return void
     */
    protected function sendReceiverMail(Mail $mail, $hash = null)
    {
        /** @var ReceiverEmailService $receiverService */
        $receiverService = $this->objectManager->get(ReceiverEmailService::class, $mail, $this->settings);
        $mail->setReceiverMail($receiverService->getReceiverEmailsString());
        /** @var SenderEmailService $senderService */
        $senderService = $this->objectManager->get(SenderEmailService::class, $mail, $this->settings);
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
                'variables' => ['hash' => $hash]
            ];
            $sent = $this->sendMailService->sendMail($email, $mail, $this->settings, 'receiver');

            if (!$sent) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('error_mail_not_created'),
                    '',
                    AbstractMessage::ERROR
                );
                $this->messageClass = 'error';
            }
        }
    }

    /**
     * Mail Generation for Sender
     *
     * @param Mail $mail
     * @return void
     */
    protected function sendSenderMail(Mail $mail)
    {
        $email = [
            'template' => 'Mail/SenderMail',
            'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
            'receiverName' => $this->mailRepository->getSenderNameFromArguments(
                $mail,
                [$this->conf['sender.']['default.'], 'senderName']
            ),
            'senderEmail' => $this->settings['sender']['email'],
            'senderName' => $this->settings['sender']['name'],
            'replyToEmail' => $this->settings['sender']['email'],
            'replyToName' => $this->settings['sender']['name'],
            'subject' => $this->settings['sender']['subject'],
            'rteBody' => $this->settings['sender']['body'],
            'format' => $this->settings['sender']['mailformat']
        ];
        $this->sendMailService->sendMail($email, $mail, $this->settings, 'sender');
    }

    /**
     * Send Optin Confirmation Mail to user
     *
     * @param Mail $mail
     * @return void
     */
    protected function sendConfirmationMail(Mail &$mail)
    {
        $email = [
            'template' => 'Mail/OptinMail',
            'receiverEmail' => $this->mailRepository->getSenderMailFromArguments($mail),
            'receiverName' => $this->mailRepository->getSenderNameFromArguments(
                $mail,
                [$this->conf['sender.']['default.'], 'senderName']
            ),
            'senderEmail' => $this->settings['sender']['email'],
            'senderName' => $this->settings['sender']['name'],
            'replyToEmail' => $this->settings['sender']['email'],
            'replyToName' => $this->settings['sender']['name'],
            'subject' => $this->contentObject->cObjGetSingle(
                $this->conf['optin.']['subject'],
                $this->conf['optin.']['subject.']
            ),
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
     * Prepare output
     *
     * @param Mail $mail
     * @return void
     */
    protected function prepareOutput(Mail $mail)
    {
        $this->view->assignMultiple(
            [
                'variablesWithMarkers' => $this->mailRepository->getVariablesWithMarkersFromMail($mail, true),
                'mail' => $mail,
                'marketingInfos' => SessionUtility::getMarketingInfos(),
                'messageClass' => $this->messageClass,
                'ttContentData' => $this->contentObject->data,
                'uploadService' => $this->uploadService,
                'powermail_rte' => $this->settings['thx']['body'],
                'powermail_all' => TemplateUtility::powermailAll($mail, 'web', $this->settings, $this->actionMethodName)
            ]
        );
        $this->view->assignMultiple($this->mailRepository->getVariablesWithMarkersFromMail($mail, true));
        $this->view->assignMultiple($this->mailRepository->getLabelsWithMarkersFromMail($mail));
    }

    /**
     * Save mail on submit
     *
     * @param Mail $mail
     * @return void
     */
    protected function saveMail(Mail $mail)
    {
        $marketingInfos = SessionUtility::getMarketingInfos();
        $mail
            ->setPid(FrontendUtility::getStoragePage($this->settings['main']['pid']))
            ->setSenderMail($this->mailRepository->getSenderMailFromArguments($mail))
            ->setSenderName($this->mailRepository->getSenderNameFromArguments($mail))
            ->setSubject($this->settings['receiver']['subject'])
            ->setReceiverMail($this->settings['receiver']['email'])
            ->setBody(DebugUtility::viewArray($this->mailRepository->getVariablesWithMarkersFromMail($mail)))
            ->setSpamFactor(SessionUtility::getSpamFactorFromSession())
            ->setTime((time() - SessionUtility::getFormStartFromSession($mail->getForm()->getUid(), $this->settings)))
            ->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'))
            ->setMarketingRefererDomain($marketingInfos['refererDomain'])
            ->setMarketingReferer($marketingInfos['referer'])
            ->setMarketingCountry($marketingInfos['country'])
            ->setMarketingMobileDevice($marketingInfos['mobileDevice'])
            ->setMarketingFrontendLanguage($marketingInfos['frontendLanguage'])
            ->setMarketingBrowserLanguage($marketingInfos['browserLanguage'])
            ->setMarketingPageFunnel($marketingInfos['pageFunnel']);
        if (FrontendUtility::isLoggedInFrontendUser()) {
            $mail->setFeuser(
                $this->userRepository->findByUid(FrontendUtility::getPropertyFromLoggedInFrontendUser('uid'))
            );
        }
        if (!ConfigurationUtility::isDisableIpLogActive()) {
            $mail->setSenderIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        }
        if ($this->settings['main']['optin'] || $this->settings['db']['hidden']) {
            $mail->setHidden(true);
        }
        foreach ($mail->getAnswers() as $answer) {
            $answer->setPid(FrontendUtility::getStoragePage($this->settings['main']['pid']));
        }
        $this->mailRepository->add($mail);
        $this->persistenceManager->persistAll();
    }

    /**
     * Confirm Double Optin
     *
     * @param int $mail mail uid
     * @param string $hash Given Hash String
     * @return void
     */
    public function optinConfirmAction($mail, $hash)
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $hash, $this]);
        $mail = $this->mailRepository->findByUid($mail);
        $this->forwardIfFormParamsDoNotMatchForOptinConfirm($mail);
        $labelKey = 'failed';

        if ($mail !== null && OptinUtility::checkOptinHash($hash, $mail)) {
            if ($mail->getHidden()) {
                $mail->setHidden(false);
                $this->mailRepository->update($mail);
                $this->persistenceManager->persistAll();

                $this->forward('create', null, null, ['mail' => $mail, 'hash' => $hash]);
            }
            $labelKey = 'done';
        }
        $this->view->assign('labelKey', $labelKey);
    }

    /**
     * Marketing Tracking Action
     *
     * @param string $referer Referer
     * @param int $language Frontend Language Uid
     * @param int $pid Page Id
     * @param int $mobileDevice Is mobile device?
     * @return void
     */
    public function marketingAction($referer = null, $language = 0, $pid = 0, $mobileDevice = 0)
    {
        SessionUtility::storeMarketingInformation($referer, $language, $pid, $mobileDevice);
    }

    /**
     * Initializes this object
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->contentObject = $this->configurationManager->getContentObject();
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $this->conf = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
        ConfigurationUtility::mergeTypoScript2FlexForm($this->settings);
        if ($this->settings['debug']['settings']) {
            GeneralUtility::devLog('Settings', $this->extensionName, 0, $this->settings);
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'Settings', [$this]);
    }

    /**
     * Initialize Action
     *
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();

        if (!isset($this->settings['staticTemplate'])) {
            $this->controllerContext = $this->buildControllerContext();
            $this->addFlashMessage(LocalizationUtility::translate('error_no_typoscript'), '', AbstractMessage::ERROR);
        }
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used for createAction() and confirmationAction()
     *
     * @return void
     */
    protected function forwardIfFormParamsDoNotMatch()
    {
        $arguments = $this->request->getArguments();
        $formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
        if (is_array($arguments['mail']) && !in_array($arguments['mail']['form'], $formsToContent)) {
            $this->forward('form');
        }
    }

    /**
     * Forward to formAction if no mail param given
     *
     * @return void
     */
    protected function forwardIfMailParamEmpty()
    {
        $arguments = $this->request->getArguments();
        if (empty($arguments['mail'])) {
            GeneralUtility::devLog('Redirect (mail empty)', $this->extensionName, 2, $arguments);
            $this->forward('form');
        }
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used in optinConfirmAction()
     *
     * @param Mail $mail
     * @return void
     */
    protected function forwardIfFormParamsDoNotMatchForOptinConfirm(Mail $mail = null)
    {
        if ($mail !== null) {
            $formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
            if (!in_array($mail->getForm()->getUid(), $formsToContent)) {
                GeneralUtility::devLog('Redirect (optin)', $this->extensionName, 2, [$formsToContent, (array)$mail]);
                $this->forward('form');
            }
        }
    }

    /**
     * Decide if the mail object should be persisted or not
     *        persist if
     *            - enabled with TypoScript AND hash is not set OR
     *            - optin is enabled AND hash is not set (even if disabled in TS)
     *
     * @param string $hash
     * @return bool
     */
    protected function isMailPersistActive($hash)
    {
        return (!empty($this->settings['db']['enable']) || !empty($this->settings['main']['optin'])) && $hash === null;
    }

    /**
     * Check if mail should be send
     *        send when
     *            - optin is deaktivated OR
     *            - optin is active AND hash is correct
     *
     * @param Mail $mail
     * @param string $hash
     * @return bool
     */
    protected function isSendMailActive(Mail $mail, $hash)
    {
        return empty($this->settings['main']['optin']) ||
            (!empty($this->settings['main']['optin']) && OptinUtility::checkOptinHash($hash, $mail));
    }

    /**
     * @return void
     */
    protected function debugVariables()
    {
        if (!empty($this->settings['debug']['variables'])) {
            GeneralUtility::devLog('Variables', $this->extensionName, 0, GeneralUtility::_POST());
        }
    }
}
