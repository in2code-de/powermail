<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Repository\UserRepository;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class ReceiverService to get email array for receivers
 * which depends if they are given by FlexForm, TypoScript, Fe_group or
 * development context
 */
class ReceiverMailReceiverPropertiesService
{
    use SignalTrait;

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
     * @var array
     */
    protected $receiverEmails = [];

    /**
     * @param Mail $mail
     * @param array $settings
     * @throws Exception
     * @throws InvalidQueryException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function __construct(Mail $mail, array $settings)
    {
        $this->mail = $mail;
        $this->settings = $settings;
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
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
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getReceiverName(): string
    {
        $receiverName = 'Powermail';
        if (!empty($this->settings['receiver']['name'])) {
            $receiverName = $this->settings['receiver']['name'];
        }

        $signalArguments = [&$receiverName, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $receiverName;
    }

    /**
     * @return void
     * @throws Exception
     * @throws InvalidQueryException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    protected function setReceiverEmails(): void
    {
        $emailArray = $this->getEmailsFromFlexForm();
        $emailArray = $this->getEmailsFromFeGroup($emailArray, $this->settings['receiver']['fe_group']);
        $emailArray = $this->getEmailsFromBeGroup($emailArray, $this->settings['receiver']['be_group']);
        $emailArray = $this->getEmailsFromPredefinedEmail($emailArray, $this->settings['receiver']['predefinedemail']);
        $emailArray = $this->overWriteEmailsWithTypoScript($emailArray);
        $emailArray = $this->getEmailFromDevelopmentContext($emailArray);

        $signalArguments = [&$emailArray, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        $this->receiverEmails = $emailArray;
    }

    /**
     * Get emails from FlexForm and parse with fluid
     *
     * @return array
     * @throws Exception
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    protected function getEmailsFromFlexForm(): array
    {
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        $emailString = TemplateUtility::fluidParseString(
            $this->settings['receiver']['email'],
            $mailRepository->getVariablesWithMarkersFromMail($this->mail)
        );
        return $this->parseEmailsFromString($emailString);
    }

    /**
     * Read emails from frontend users within a group
     *
     * @param array $emailArray
     * @param int $uid fe_groups.uid
     * @return array Array with emails
     * @throws Exception
     * @throws InvalidQueryException
     */
    protected function getEmailsFromFeGroup(array $emailArray, int $uid): array
    {
        if ((int)$this->settings['receiver']['type'] === 1 && !empty($uid)) {
            $userRepository = ObjectUtility::getObjectManager()->get(UserRepository::class);
            $users = $userRepository->findByUsergroup($uid);
            $emailArray = [];
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
     * @throws Exception
     */
    protected function getEmailsFromBeGroup(array $emailArray, int $uid): array
    {
        // todo
        if ((int)$this->settings['receiver']['type'] === 3 && !empty($uid)) {
            $beUserRepository = ObjectUtility::getObjectManager()->get(BackendUserRepository::class);
            $beGroupRepository = ObjectUtility::getObjectManager()->get(BackendUserGroupRepository::class);
            $userGroup = $beGroupRepository->findByUid($uid);
            $demand = ObjectUtility::getObjectManager()->get(Demand::class);
            $demand->setBackendUserGroup($userGroup);
            $users = $beUserRepository->findDemanded($demand);
            $emailArray = [];
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
     */
    protected function getEmailsFromPredefinedEmail(array $emailArray, string $predefinedString): array
    {
        if ((int)$this->settings['receiver']['type'] === 2 && !empty($predefinedString)) {
            $receiverString = '';
            TypoScriptUtility::overwriteValueFromTypoScript(
                $receiverString,
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
     */
    protected function overWriteEmailsWithTypoScript(array $emailArray): array
    {
        $receiverString = '';
        TypoScriptUtility::overwriteValueFromTypoScript(
            $receiverString,
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
     * Read emails from String and split it on break, pipe, comma and semicolon
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
                ','
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
