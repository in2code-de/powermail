<?php
declare(strict_types = 1);
namespace In2code\Powermail\Controller;

use In2code\Powermail\DataProcessor\DataProcessorRunner;
use In2code\Powermail\Domain\Factory\MailFactory;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Domain\Service\Mail\SendDisclaimedMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendOptinConfirmationMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendReceiverMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendSenderMailPreflight;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Finisher\FinisherRunner;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\HashUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use function in_array;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as ExtbaseAnnotation;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidControllerNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;

use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class FormController
 */
class FormController extends AbstractController
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var DataProcessorRunner
     */
    protected $dataProcessorRunner;

    /**
     * @return ResponseInterface
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function formAction(): ResponseInterface
    {
        if (!ArrayUtility::isValidPath($this->settings, 'main/form')) {
            return $this->htmlResponse();
        }
        /** @var Form $form */
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

        return $this->htmlResponse();
    }

    /**
     * Rewrite Arguments to receive a clean mail object in confirmationAction
     *
     * @return void
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidArgumentNameException
     * @throws InvalidQueryException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws DeprecatedException
     * @noinspection PhpUnused
     */
    public function initializeConfirmationAction(): void
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamEmpty();
        $this->reformatParamsForAction();
        $this->debugVariables();
    }

    /**
     * Show a "Are your values ok?" message before final submit (if turned on)
     *
     * @param Mail $mail
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\UploadValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\InputValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\PasswordValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\CaptchaValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\SpamShieldValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\UniqueValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\ForeignValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\CustomValidator", param="mail")
     * @return ResponseInterface
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function confirmationAction(Mail $mail): ResponseInterface
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $this]);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->dataProcessorRunner->callDataProcessors(
            $mail,
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );
        $this->prepareOutput($mail);

        return $this->htmlResponse();
    }

    /**
     * Rewrite Arguments to receive a clean mail object in createAction
     *
     * @return void
     * @throws Exception
     * @throws InvalidArgumentNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws DeprecatedException
     * @noinspection PhpUnused
     */
    public function initializeCreateAction(): void
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamEmpty();
        $this->reformatParamsForAction();
        $this->debugVariables();
    }

    /**
     * @param Mail $mail
     * @param string $hash
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\UploadValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\InputValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\PasswordValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\CaptchaValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\SpamShieldValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\UniqueValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\ForeignValidator", param="mail")
     * @ExtbaseAnnotation\Validate("In2code\Powermail\Domain\Validator\CustomValidator", param="mail")
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws UnknownObjectException
     * @throws InvalidControllerNameException
     * @throws \Exception
     * @noinspection PhpUnused
     */
    public function createAction(Mail $mail, string $hash = ''): ResponseInterface
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $hash, $this]);
        /** @noinspection PhpUnhandledExceptionInspection */
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
        if ($this->isNoOptin($mail, $hash)) {
            $this->sendMailPreflight($mail, $hash);
        } else {
            $mailPreflight = $this->objectManager->get(
                SendOptinConfirmationMailPreflight::class,
                $this->settings,
                $this->conf
            );
            $mailPreflight->sendOptinConfirmationMail($mail);
            $this->view->assign('optinActive', true);
        }
        if ($this->isPersistActive()) {
            $this->mailRepository->update($mail);
            $this->persistenceManager->persistAll();
        }

        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'AfterSubmitView', [$mail, $hash, $this]);
        $this->prepareOutput($mail);

        $finisherRunner = $this->objectManager->get(FinisherRunner::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $finisherRunner->callFinishers(
            $mail,
            $this->isNoOptin($mail, $hash),
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );

        return $this->htmlResponse();
    }

    /**
     * @param Mail $mail
     * @param string $hash
     * @return void
     */
    protected function sendMailPreflight(Mail $mail, string $hash = ''): void
    {
        try {
            if ($this->isSenderMailEnabled() && $this->mailRepository->getSenderMailFromArguments($mail)) {
                $mailPreflight = $this->objectManager->get(
                    SendSenderMailPreflight::class,
                    $this->settings,
                    $this->conf
                );
                $mailPreflight->sendSenderMail($mail);
            }
            if ($this->isReceiverMailEnabled()) {
                $mailPreflight = $this->objectManager->get(SendReceiverMailPreflight::class, $this->settings);
                $isSent = $mailPreflight->sendReceiverMail($mail, $hash);
                if ($isSent === false) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate('error_mail_not_created'),
                        '',
                        AbstractMessage::ERROR
                    );
                    $this->messageClass = 'error';
                }
            }
        } catch (\Exception $exception) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->critical('Mail could not be sent', [$exception->getMessage()]);
        }
    }

    /**
     * @param Mail $mail
     * @return void
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function prepareOutput(Mail $mail): void
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
     * @param Mail $mail
     * @return void
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @codeCoverageIgnore
     */
    protected function saveMail(Mail $mail): void
    {
        $mailFactory = $this->objectManager->get(MailFactory::class);
        $mailFactory->prepareMailForPersistence($mail, $this->settings);
        $this->mailRepository->add($mail);
        $this->persistenceManager->persistAll();
    }

    /**
     * Confirm Double Optin
     *
     * @param int $mail mail uid
     * @param string $hash Given Hash String
     * @return ResponseInterface
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function optinConfirmAction(int $mail, string $hash): ResponseInterface
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $hash, $this]);
        $mail = $this->mailRepository->findByUid($mail);
        $response = $this->forwardIfFormParamsDoNotMatchForOptinConfirm($mail);
        if ($response !== null) {
            return $response;
        }
        $labelKey = 'failed';

        /** @noinspection PhpUnhandledExceptionInspection */
        if ($mail !== null && HashUtility::isHashValid($hash, $mail)) {
            if ($mail->getHidden()) {
                $mail->setHidden(false);
                $this->mailRepository->update($mail);
                $this->persistenceManager->persistAll();
                $this->signalDispatch(__CLASS__, __FUNCTION__ . 'AfterPersist', [$mail, $hash, $this]);

                return (new ForwardResponse('create'))->withArguments(['mail' => $mail, 'hash' => $hash]);
            }
            $labelKey = 'done';
        }
        $this->view->assign('labelKey', $labelKey);

        return $this->htmlResponse();
    }

    /**
     * @param int $mail
     * @param string $hash
     * @return ResponseInterface
     * @throws \Exception
     * @noinspection PhpUnused
     */
    public function disclaimerAction(int $mail, string $hash): ResponseInterface
    {
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [$mail, $hash, $this]);
        $mail = $this->mailRepository->findByUid($mail);
        $status = false;
        if ($mail !== null && HashUtility::isHashValid($hash, $mail, 'disclaimer')) {
            $mailService = $this->objectManager->get(SendDisclaimedMailPreflight::class, $this->settings, $this->conf);
            $mailService->sendMail($mail);
            $this->mailRepository->removeFromDatabase($mail->getUid());
            $status = true;
        }
        $this->view->assign('status', $status);

        return $this->htmlResponse();
    }

    /**
     * @param string $referer Referer
     * @param int $language Frontend Language Uid
     * @param int $pid Page Id
     * @param bool $mobileDevice Is mobile device?
     * @return ResponseInterface
     * @noinspection PhpUnused
     * @codeCoverageIgnore
     */
    public function marketingAction(
        string $referer = '',
        int $language = 0,
        int $pid = 0,
        bool $mobileDevice = false
    ): ResponseInterface {
        SessionUtility::storeMarketingInformation($referer, $language, $pid, $mobileDevice, $this->settings);
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode([]));
        return $response;
    }

    /**
     * Initializes this object
     *
     * @return void
     * @codeCoverageIgnore
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    public function initializeObject()
    {
        // @extensionScannerIgnoreLine Seems to be a false positive: getContentObject() is still correct in 9.0
        $this->contentObject = $this->configurationManager->getContentObject();
        $configurationService = $this->objectManager->get(ConfigurationService::class);
        $this->conf = $configurationService->getTypoScriptConfiguration();
        $this->settings = ConfigurationUtility::mergeTypoScript2FlexForm($this->settings);
        if (ArrayUtility::isValidPath($this->settings, 'debug/settings') && $this->settings['debug']['settings']) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->info('Powermail settings', $this->settings);
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'Settings', [$this, &$this->settings]);
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used for createAction() and confirmationAction()
     *
     * @throws StopActionException
     */
    protected function forwardIfFormParamsDoNotMatch(): void
    {
        $arguments = $this->request->getArguments();
        if (isset($arguments['mail'])) {
            $formUid = null;
            if ($arguments['mail'] instanceof Mail) {
                $form = $arguments['mail']->getForm();
                if (null !== $form) {
                    $formUid = $form->getUid();
                }
            } else {
                $formUid = $arguments['mail']['form'] ?? null;
            }

            $formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
            if (null === $formUid || in_array($formUid, $formsToContent, false)) {
                return;
            }
            $this->forward('form');
        }
    }

    /**
     * Forward to formAction if no mail param given
     *
     * @return ForwardResponse|null
     */
    protected function forwardIfMailParamEmpty(): ?ForwardResponse
    {
        $arguments = $this->request->getArguments();
        if (empty($arguments['mail'])) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->warning('Redirect (mail empty)', $arguments);

            return new ForwardResponse('form');
        }
        return null;
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used in optinConfirmAction()
     *
     * @param Mail|null $mail
     * @return ResponseInterface|null
     */
    protected function forwardIfFormParamsDoNotMatchForOptinConfirm(Mail $mail = null): ?ResponseInterface
    {
        if ($mail !== null) {
            $formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
            if (!in_array($mail->getForm()->getUid(), $formsToContent)) {
                $logger = ObjectUtility::getLogger(__CLASS__);
                $logger->warning('Redirect (optin)', [$formsToContent, (array)$mail]);

                return new ForwardResponse('form');
            }
        }

        return null;
    }

    /**
     * Always forward to formAction if a validation fails. Otherwise it could happen that when
     * a validator for createAction fails, confirmationAction is called (if function is turned on) and same validators
     * are firing again
     *
     * @return ResponseInterface|null
     */
    protected function forwardToReferringRequest(): ?ResponseInterface
    {
        $originalRequest = clone $this->request;
        $this->request->setOriginalRequest($originalRequest);
        $this->request->setOriginalRequestMappingResults($this->arguments->validate());

        $response = new ForwardResponse('form');
        return $response->withArgumentsValidationResult($this->arguments->validate());
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
    protected function isMailPersistActive(string $hash = ''): bool
    {
        return ($this->isPersistActive() || !empty($this->settings['main']['optin'])) && $hash === '';
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
     * @throws \Exception
     */
    protected function isNoOptin(Mail $mail, string $hash = ''): bool
    {
        return empty($this->settings['main']['optin']) ||
            (!empty($this->settings['main']['optin']) && HashUtility::isHashValid($hash, $mail));
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function debugVariables(): void
    {
        if (!empty($this->settings['debug']['variables'])) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->info('Variables', GeneralUtility::_POST());
        }
    }

    /**
     * @return bool
     */
    protected function isPersistActive(): bool
    {
        return $this->settings['db']['enable'] === '1';
    }

    /**
     * @return bool
     */
    protected function isSenderMailEnabled(): bool
    {
        return $this->settings['sender']['enable'] === '1';
    }

    /**
     * @return bool
     */
    protected function isReceiverMailEnabled(): bool
    {
        return $this->settings['receiver']['enable'] === '1';
    }

    /**
     * @param DataProcessorRunner $dataProcessorRunner
     * @return void
     */
    public function injectDataProcessorRunner(DataProcessorRunner $dataProcessorRunner): void
    {
        $this->dataProcessorRunner = $dataProcessorRunner;
    }

    /**
     * @param PersistenceManager $persistenceManager
     * @return void
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }
}
