<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\User;
use In2code\Powermail\Domain\Repository\BackendUserRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent;
use In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class ReceiverService to get email array for receivers
 * which depends if they are given by FlexForm, TypoScript, Fe_group or
 * development context
 */
class ReceiverMailReceiverPropertiesService
{
    const RECEIVERS_DEFAULT = 0;
    const RECEIVERS_FRONTENDGROUP = 1;
    const RECEIVERS_PREDEFINED = 2;
    const RECEIVERS_BACKENDGROUP = 3;

    /**
     * @var Mail|null
     */
    protected ?Mail $mail = null;

    /**
     * TypoScript settings as plain array
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * TypoScript configuration for cObject parsing
     *
     * @var array
     */
    protected array $configuration = [];

    /**
     * @var array
     */
    protected array $receiverEmails = [];

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param Mail $mail
     * @param array $settings
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     */
    public function __construct(Mail $mail, array $settings)
    {
        $this->mail = $mail;
        $this->settings = $settings;
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $this->setReceiverEmails();
    }

    /**
     * @return array
     */
    public function getReceiverEmails(): array
    {
        return $this->receiverEmails;
    }

    /**
     * @return string
     */
    public function getReceiverEmailsString(): string
    {
        return implode(PHP_EOL, $this->receiverEmails);
    }

    /**
     * Get receiver name with fallback
     *
     * @return string
     */
    public function getReceiverName(): string
    {
        $receiverName = 'Powermail';
        if (!empty($this->settings['receiver']['name'])) {
            $receiverName = $this->settings['receiver']['name'];
        }

        /** @var ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent $event */
        $event = $this->eventDispatcher->dispatch(
            GeneralUtility::makeInstance(
                ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent::class,
                $receiverName,
                $this
            )
        );
        return $event->getReceiverName();
    }

    /**
     * @return void
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     */
    protected function setReceiverEmails(): void
    {
        $emailArray = $this->getEmailsFromFlexForm();
        $emailArray = $this->getEmailsFromFeGroup($emailArray, (int)($this->settings['receiver']['fe_group'] ?? 0));
        $emailArray = $this->getEmailsFromBeGroup($emailArray, (int)($this->settings['receiver']['be_group'] ?? 0));
        $emailArray = $this->getEmailsFromPredefinedEmail(
            $emailArray,
            (string)($this->settings['receiver']['predefinedemail'] ?? '')
        );
        $emailArray = $this->overWriteEmailsWithTypoScript($emailArray);
        $emailArray = $this->getEmailFromDevelopmentContext($emailArray);

        /** @var ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            GeneralUtility::makeInstance(
                ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent::class,
                $emailArray,
                $this
            )
        );
        $this->receiverEmails = $event->getEmailArray();
    }

    /**
     * Get emails from FlexForm and parse with fluid
     *
     * @return array
     */
    protected function getEmailsFromFlexForm(): array
    {
        if ((int)($this->settings['receiver']['type'] ?? 0) === self::RECEIVERS_DEFAULT) {
            $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
            $emailString = TemplateUtility::fluidParseString(
                $this->settings['receiver']['email'],
                $mailRepository->getVariablesWithMarkersFromMail($this->mail)
            );
            return $this->parseEmailsFromString($emailString);
        }
        return [];
    }

    /**
     * Read emails from frontend users within a group
     *
     * @param array $emailArray
     * @param int $uid fe_groups.uid
     * @return array Array with emails
     * @throws InvalidQueryException
     */
    protected function getEmailsFromFeGroup(array $emailArray, int $uid): array
    {
        if ((int)($this->settings['receiver']['type'] ?? 0) === self::RECEIVERS_FRONTENDGROUP && !empty($uid)) {
            $userRepository = GeneralUtility::makeInstance(UserRepository::class);
            $users = $userRepository->findByUsergroup($uid);
            $emailArray = [];
            /** @var User $user */
            foreach ($users as $user) {
                if (GeneralUtility::validEmail($user->getEmail())) {
                    $emailArray[] = $user->getEmail();
                }
            }
        }
        return $emailArray;
    }

    /**
     * Read emails from backend users within a group
     *
     * @param array $emailArray
     * @param int $uid be_groups.uid
     * @return array
     * @throws InvalidQueryException
     */
    protected function getEmailsFromBeGroup(array $emailArray, int $uid): array
    {
        if ((int)($this->settings['receiver']['type'] ?? 0) === self::RECEIVERS_BACKENDGROUP && !empty($uid)) {
            /** @var BackendUserRepository $beUserRepository */
            $beUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
            $query = $beUserRepository->createQuery();
            $users = $query->matching($query->contains('usergroup', $uid))->execute();
            $emailArray = [];
            /** @var User $user */
            foreach ($users as $user) {
                if (GeneralUtility::validEmail($user->getEmail())) {
                    $emailArray[] = $user->getEmail();
                }
            }
        }
        return $emailArray;
    }

    /**
     * Get emails from predefined TypoScript
     *
     *      plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {
     *          1.email = TEXT
     *          1.email.value = email1@domain.org, email2@domain.org
     *      }
     *
     * @param array $emailArray
     * @param string $predefinedString
     * @return array
     * @throws ExceptionExtbaseObject
     */
    protected function getEmailsFromPredefinedEmail(array $emailArray, string $predefinedString): array
    {
        if ((int)($this->settings['receiver']['type'] ?? 0) === self::RECEIVERS_PREDEFINED && !empty($predefinedString)) {
            $receiverString = TypoScriptUtility::overwriteValueFromTypoScript(
                '',
                $this->configuration['receiver.']['predefinedReceiver.'][$predefinedString . '.'],
                'email'
            );
            $emailArray = $this->parseEmailsFromString($receiverString);
        }
        return $emailArray;
    }

    /**
     * Get email string from TypoScript overwrite
     *
     * @param array $emailArray
     * @return array
     * @throws ExceptionExtbaseObject
     */
    protected function overWriteEmailsWithTypoScript(array $emailArray): array
    {
        $receiverString = TypoScriptUtility::overwriteValueFromTypoScript(
            '',
            $this->configuration['receiver.']['overwrite.'],
            'email'
        );
        $overwriteReceivers = $this->parseEmailsFromString($receiverString);
        if (!empty($overwriteReceivers)) {
            $emailArray = $overwriteReceivers;
        }
        return $emailArray;
    }

    /**
     * Get email from development context
     *
     * @param array $emailArray
     * @return array
     */
    protected function getEmailFromDevelopmentContext(array $emailArray): array
    {
        if (ConfigurationUtility::getDevelopmentContextEmail() !== '') {
            $emailArray = [ConfigurationUtility::getDevelopmentContextEmail()];
        }
        return $emailArray;
    }

    /**
     * Read emails from String and split it on break, pipe, comma, semicolon and space
     *
     * @param string $string Any given string from a textarea with some emails
     * @return array Array with emails
     */
    protected function parseEmailsFromString(string $string): array
    {
        $array = [];
        $string = str_replace(
            [
                PHP_EOL,
                '|',
                ',',
                ' ',
            ],
            ';',
            $string
        );
        $arr = GeneralUtility::trimExplode(';', $string, true);
        foreach ($arr as $email) {
            if (GeneralUtility::validEmail($email)) {
                $array[] = $email;
            }
        }
        return $array;
    }
}
