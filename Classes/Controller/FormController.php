<?php

declare(strict_types=1);

namespace In2code\Powermail\Controller;

use Exception;
use In2code\Powermail\DataProcessor\DataProcessorRunner;
use In2code\Powermail\Domain\Factory\MailFactory;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Domain\Service\Mail\SendDisclaimedMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendOptinConfirmationMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendReceiverMailPreflight;
use In2code\Powermail\Domain\Service\Mail\SendSenderMailPreflight;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Events\CheckIfMailIsAllowedToSaveEvent;
use In2code\Powermail\Events\FormControllerConfirmationActionEvent;
use In2code\Powermail\Events\FormControllerCreateActionAfterMailDbSavedEvent;
use In2code\Powermail\Events\FormControllerCreateActionAfterSubmitViewEvent;
use In2code\Powermail\Events\FormControllerCreateActionBeforeRenderViewEvent;
use In2code\Powermail\Events\FormControllerDisclaimerActionBeforeRenderViewEvent;
use In2code\Powermail\Events\FormControllerFormActionEvent;
use In2code\Powermail\Events\FormControllerInitializeObjectEvent;
use In2code\Powermail\Events\FormControllerOptinConfirmActionAfterPersistEvent;
use In2code\Powermail\Events\FormControllerOptinConfirmActionBeforeRenderViewEvent;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Finisher\FinisherRunner;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\HashUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;
use In2code\Powermail\Utility\TemplateUtility;
use function in_array;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as ExtbaseAnnotation;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class FormController
 */
class FormController extends AbstractController
{
    public function __construct(
        protected FormRepository $formRepository,
        protected FieldRepository $fieldRepository,
        protected MailRepository $mailRepository,
        protected UploadService $uploadService,
        protected EventDispatcherInterface $eventDispatcher,
        protected DataProcessorRunner $dataProcessorRunner,
        protected PersistenceManager $persistenceManager,
    ) {
    }

    public function initializeAction(): void
    {
        parent::initializeAction();
        $this->contentObject = $this->request->getAttribute('currentContentObject');
    }

    /**
     * @noinspection PhpUnused
     */
    public function formAction(): ResponseInterface
    {
        if (!ArrayUtility::isValidPath($this->settings, 'main/form')) {
            return $this->htmlResponse();
        }

        /** @var Form $form */
        $form = $this->formRepository->findByUid($this->settings['main']['form']);

        /** @var FormControllerFormActionEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new FormControllerFormActionEvent($form, $this)
        );
        if ($event->getViewVariables()) {
            $this->view->assignMultiple($event->getViewVariables());
        }
        $form = $event->getForm();
        SessionUtility::saveFormStartInSession($this->settings, $form);
        $this->view->assignMultiple(
            [
                'form' => $form,
                'ttContentData' => $this->contentObject->data,
                'messageClass' => $this->messageClass,
                'action' => ($this->settings['main']['confirmation'] ? 'confirmation' : 'create'),
            ]
        );

        return $this->htmlResponse();
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws NoSuchArgumentException
     * @throws PropagateResponseException
     * @throws DeprecatedException
     */
    public function initializeConfirmationAction(): void
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamIsEmpty();
        $this->reformatParamsForAction();
    }

    /**
     * Show a "Are your values ok?" message before final submit (if turned on)
     *
     * @param Mail $mail
     * @return ResponseInterface
     * @throws Exception
     */
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\UploadValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\InputValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\PasswordValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\CaptchaValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\SpamShieldValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\UniqueValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\ForeignValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\CustomValidator::class, 'param' => 'mail'])]
    public function confirmationAction(Mail $mail): ResponseInterface
    {
        if ($mail->getUid() !== null) {
            return (new ForwardResponse('form'))->withoutArguments();
        }

        $event = new FormControllerConfirmationActionEvent($mail, $this);
        $this->eventDispatcher->dispatch($event);
        $mail = $event->getMail();

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
     * @throws DeprecatedException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws NoSuchArgumentException
     * @throws PropagateResponseException
     */
    public function initializeCreateAction(): void
    {
        $this->forwardIfFormParamsDoNotMatch();
        $this->forwardIfMailParamIsEmpty();
        $this->reformatParamsForAction();
    }

    /**
     * @param Mail $mail
     * @param string $hash
     * @return ResponseInterface
     * @throws Exception
     */
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\UploadValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\InputValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\PasswordValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\CaptchaValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\SpamShieldValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\UniqueValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\ForeignValidator::class, 'param' => 'mail'])]
    #[ExtbaseAnnotation\Validate(['validator' => \In2code\Powermail\Domain\Validator\CustomValidator::class, 'param' => 'mail'])]
    public function createAction(Mail $mail, string $hash = ''): ResponseInterface
    {
        if ($mail->getUid() !== null && !HashUtility::isHashValid($hash, $mail)) {
            return (new ForwardResponse('form'))->withoutArguments();
        }

        $event = new FormControllerCreateActionBeforeRenderViewEvent($mail, $hash, $this);
        $this->eventDispatcher->dispatch($event);
        $mail = $event->getMail();
        $hash = $event->getHash();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->dataProcessorRunner->callDataProcessors(
            $mail,
            $this->actionMethodName,
            $this->settings,
            $this->contentObject
        );
        if ($this->isMailPersistActive($hash)) {
            $event = new CheckIfMailIsAllowedToSaveEvent($mail);
            $this->eventDispatcher->dispatch($event);
            $isSavingOfMailAllowed = $event->isSavingOfMailAllowed();
            if ($isSavingOfMailAllowed) {
                $this->saveMail($mail);
            }

            $event = new FormControllerCreateActionAfterMailDbSavedEvent($mail, $this, $hash);
            $this->eventDispatcher->dispatch($event);
            $mail = $event->getMail();
            $hash = $event->getHash();
        }

        if ($this->isNoOptin($mail, $hash)) {
            $this->sendMailPreflight($mail, $hash);
        } else {
            $mailPreflight = GeneralUtility::makeInstance(
                SendOptinConfirmationMailPreflight::class,
                $this->settings,
                $this->conf,
                $this->request
            );
            $mailPreflight->sendOptinConfirmationMail($mail);
            $this->view->assign('optinActive', true);
        }

        if ($this->isMailPersistActive($hash) && $isSavingOfMailAllowed) {
            $this->mailRepository->update($mail);
            $this->persistenceManager->persistAll();
        }

        $this->cleanupCaptchaDataInSession($mail);

        $event = new FormControllerCreateActionAfterSubmitViewEvent($mail, $hash, $this);
        $this->eventDispatcher->dispatch($event);
        $mail = $event->getMail();
        $hash = $event->getHash();

        $this->prepareOutput($mail);

        $finisherRunner = GeneralUtility::makeInstance(FinisherRunner::class);
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

    protected function sendMailPreflight(Mail $mail, string $hash = ''): void
    {
        try {
            if ($this->isSenderMailEnabled() && $this->mailRepository->getSenderMailFromArguments($mail)) {
                $mailPreflight = GeneralUtility::makeInstance(
                    SendSenderMailPreflight::class,
                    $this->settings,
                    $this->conf,
                    $this->request
                );
                $mailPreflight->sendSenderMail($mail);
            }

            if ($this->isReceiverMailEnabled()) {
                $mailPreflight = GeneralUtility::makeInstance(
                    SendReceiverMailPreflight::class,
                    $this->settings,
                    $this->request
                );
                $isSent = $mailPreflight->sendReceiverMail($mail, $hash);
                if ($isSent === false) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate('error_mail_not_created'),
                        '',
                        \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
                    );
                    $this->messageClass = 'error';
                }
            }
        } catch (Throwable $throwable) {
            $logger = ObjectUtility::getLogger(self::class);
            $logger->critical('Mail could not be sent', [$throwable->getMessage()]);
        }
    }

    /**
     * @throws InvalidConfigurationTypeException
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
                'powermail_all' => TemplateUtility::powermailAll($mail, 'web', $this->settings, $this->actionMethodName),
            ]
        );
        $this->view->assignMultiple($this->mailRepository->getVariablesWithMarkersFromMail($mail, true));
        $this->view->assignMultiple($this->mailRepository->getLabelsWithMarkersFromMail($mail));
    }

    /**
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws IllegalObjectTypeException
     * @codeCoverageIgnore
     */
    protected function saveMail(Mail $mail): void
    {
        $mailFactory = GeneralUtility::makeInstance(MailFactory::class);
        $mailFactory->prepareMailForPersistence($mail, $this->settings);

        $this->mailRepository->add($mail);
        $this->persistenceManager->persistAll();
    }

    /**
     * Confirm Double Optin
     *
     * @param int $mailUid
     * @param string $hash Given Hash String
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function optinConfirmAction(int $mail, string $hash): ResponseInterface
    {
        $mail = $this->mailRepository->findByUid($mail);
        $labelKey = 'failed';

        /** @noinspection PhpUnhandledExceptionInspection */
        if ($mail instanceof \In2code\Powermail\Domain\Model\Mail && HashUtility::isHashValid($hash, $mail)) {
            $event = GeneralUtility::makeInstance(
                FormControllerOptinConfirmActionBeforeRenderViewEvent::class,
                $mail,
                $hash,
                $this
            );

            $this->eventDispatcher->dispatch($event);
            $mail = $event->getMail();
            $hash = $event->getHash();

            $this->forwardIfFormParamsDoNotMatchForOptinConfirm($mail);

            if ($mail->getHidden() && $this->isPersistActive()) {
                $mail->setHidden(false);
                $this->mailRepository->update($mail);
                $this->persistenceManager->persistAll();
                $event = GeneralUtility::makeInstance(
                    FormControllerOptinConfirmActionAfterPersistEvent::class,
                    $mail,
                    $hash,
                    $this
                );

                $this->eventDispatcher->dispatch($event);

                $mail = $event->getMail();
                $hash = $event->getHash();

                return (new ForwardResponse('create'))->withArguments(['mail' => $mail, 'hash' => $hash]);
            }

            if ($mail->getHidden() && !$this->isPersistActive()) {
                $this->prepareOutput($mail);
                DatabaseUtility::deleteMailAndAnswersFromDatabase($mail->getUid());
                return (new ForwardResponse('create'))->withArguments(['mail' => $mail, 'hash' => $hash]);
            }

            $labelKey = 'done';
        }

        $this->view->assign('labelKey', $labelKey);

        return $this->htmlResponse();
    }

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function disclaimerAction(int $mail, string $hash): ResponseInterface
    {
        $mail = $this->mailRepository->findByUid($mail);
        $status = false;

        if ($mail instanceof \In2code\Powermail\Domain\Model\Mail && HashUtility::isHashValid($hash, $mail, 'disclaimer')) {
            $event = GeneralUtility::makeInstance(
                FormControllerDisclaimerActionBeforeRenderViewEvent::class,
                $mail,
                $hash,
                $this
            );
            $this->eventDispatcher->dispatch($event);

            $mail = $event->getMail();

            $mailService = GeneralUtility::makeInstance(
                SendDisclaimedMailPreflight::class,
                $this->settings,
                $this->conf,
                $this->request
            );
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
     * @codeCoverageIgnore
     * @throws Exception
     */
    public function initializeObject(): void
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->conf = $configurationService->getTypoScriptConfiguration();
        $this->settings = ConfigurationUtility::mergeTypoScript2FlexForm($this->settings);
        /** @var FormControllerInitializeObjectEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new FormControllerInitializeObjectEvent($this->settings, $this)
        );
        $this->settings = $event->getSettings();
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used for createAction() and confirmationAction()
     */
    protected function forwardIfFormParamsDoNotMatch(): bool
    {
        $arguments = $this->request->getArguments();
        if (isset($arguments['mail'])) {
            $formUid = null;
            if ($arguments['mail'] instanceof Mail) {
                $form = $arguments['mail']->getForm();
                if ($form !== null) {
                    $formUid = $form->getUid();
                }
            } else {
                $formUid = $arguments['mail']['form'] ?? null;
            }

            $formsToContent = GeneralUtility::intExplode(',', ($this->settings['main']['form'] ?? ''));
            if (!($formUid === null || in_array($formUid, $formsToContent, false))) {
                $response = (new ForwardResponse('form'))->withArguments($this->request->getArguments());
                throw new PropagateResponseException($response);
            }
        }
        return false;
    }

    /**
     * Forward to formAction if no mail param given
     */
    protected function forwardIfMailParamIsEmpty(): bool
    {
        $arguments = $this->request->getArguments();
        if (empty($arguments['mail'])) {
            $response = new ForwardResponse('form');
            throw new PropagateResponseException($response);
        }
        return false;
    }

    /**
     * Forward to formAction if wrong form in plugin variables given
     *        used in optinConfirmAction()
     */
    protected function forwardIfFormParamsDoNotMatchForOptinConfirm(?Mail $mail = null): bool
    {
        if ($mail instanceof \In2code\Powermail\Domain\Model\Mail) {
            $formsToContent = GeneralUtility::intExplode(',', $this->settings['main']['form']);
            if (!in_array($mail->getForm()->getUid(), $formsToContent)) {
                $logger = ObjectUtility::getLogger(self::class);
                $logger->warning('Redirect (optin)', [$formsToContent, (array)$mail]);

                $response = new ForwardResponse('form');
                throw new PropagateResponseException($response);
            }
        }

        return false;
    }

    /**
     * Always forward to formAction if a validation fails. Otherwise, it could happen that when
     * a validator for createAction fails, confirmationAction is called (if function is turned on) and same validators
     * are firing again
     */
    protected function forwardToReferringRequest(): ?ResponseInterface
    {
        $response = new ForwardResponse('form');
        return $response->withArgumentsValidationResult($this->arguments->validate());
    }

    /**
     * Decide if the mail object should be persisted or not
     *        persist if
     *            - enabled with TypoScript AND hash is not set OR
     *            - optin is enabled AND hash is not set (even if disabled in TS)
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
     * @throws Exception
     */
    protected function isNoOptin(Mail $mail, string $hash = ''): bool
    {
        return empty($this->settings['main']['optin']) ||
            (!empty($this->settings['main']['optin']) && HashUtility::isHashValid($hash, $mail));
    }

    protected function isPersistActive(): bool
    {
        return $this->settings['db']['enable'] === '1';
    }

    protected function isSenderMailEnabled(): bool
    {
        return $this->settings['sender']['enable'] === '1';
    }

    protected function isReceiverMailEnabled(): bool
    {
        return $this->settings['receiver']['enable'] === '1';
    }

    protected function cleanupCaptchaDataInSession(Mail $mail): void
    {
        /** @var FormRepository $formRepository */
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        /** @var Form $form */
        $form = $mail->getForm();
        if (
            count($formRepository->hasCaptcha($form))
            && $this->settings['main']['form'] === $this->request->getArguments()['mail']['form']
        ) {
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                if ($answer->getField() && $answer->getField()->getType() === 'captcha') {
                    SessionUtility::setCaptchaSession('', (int)$answer->getUid());
                }
            }
        }
    }

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return parent::processRequest($request);
        } catch (PropagateResponseException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            $logger = ObjectUtility::getLogger(__CLASS__);
            $logger->critical('An error occurred: ', [$e->getMessage()]);
            return (new ForwardResponse('form'))->withoutArguments();
        }
    }
}
