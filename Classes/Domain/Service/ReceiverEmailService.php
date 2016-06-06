<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TemplateUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class ReceiverService to get email array for receivers
 * which depends if they are given by FlexForm, TypoScript, Fe_group or
 * development context
 *
 * @package In2code\Powermail\Domain\Service
 */
class ReceiverEmailService
{
    use SignalTrait;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

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
     */
    public function __construct(Mail $mail, array $settings)
    {
        $this->mail = $mail;
        $this->settings = $settings;
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = GeneralUtility::makeInstance(ObjectManager::class)->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * @return array
     */
    public function getReceiverEmails()
    {
        return $this->receiverEmails;
    }

    /**
     * @return string
     */
    public function getReceiverEmailsString()
    {
        return implode(PHP_EOL, $this->receiverEmails);
    }

    /**
     * Set receiver mails
     *
     * @return void
     */
    public function setReceiverEmails()
    {
        // get mails from FlexForm
        $emailArray = $this->getEmailsFromFlexForm();

        // get mails from fe_group
        if ((int)$this->settings['receiver']['type'] === 1 && !empty($this->settings['receiver']['fe_group'])) {
            $emailArray = $this->getEmailsFromFeGroup($this->settings['receiver']['fe_group']);
        }

        // get mails from predefined emailconfiguration
        if ((int)$this->settings['receiver']['type'] === 2 && !empty($this->settings['receiver']['predefinedemail'])) {
            $emailArray = $this->getEmailsFromPredefinedEmail($this->settings['receiver']['predefinedemail']);
        }

        // get mails from overwrite typoscript settings
        $overwriteReceivers = $this->overWriteEmailsWithTypoScript();
        if (!empty($overwriteReceivers)) {
            $emailArray = $overwriteReceivers;
        }

        // get mail from development context
        if (ConfigurationUtility::getDevelopmentContextEmail()) {
            $emailArray = [ConfigurationUtility::getDevelopmentContextEmail()];
        }
        $this->signalDispatch(__CLASS__, __FUNCTION__, [$emailArray, $this]);
        $this->receiverEmails = $emailArray;
    }

    /**
     * Get emails from FlexForm and parse with fluid
     *
     * @return array
     */
    protected function getEmailsFromFlexForm()
    {
        $emailString = TemplateUtility::fluidParseString(
            $this->settings['receiver']['email'],
            $this->mailRepository->getVariablesWithMarkersFromMail($this->mail)
        );
        return $this->parseEmailsFromString($emailString);
    }

    /**
     * Read emails from frontend users within a group
     *
     * @param int $uid fe_groups Uid
     * @return array Array with emails
     */
    protected function getEmailsFromFeGroup($uid)
    {
        $users = $this->userRepository->findByUsergroup($uid);
        $array = [];
        foreach ($users as $user) {
            if (GeneralUtility::validEmail($user->getEmail())) {
                $array[] = $user->getEmail();
            }
        }
        return $array;
    }

    /**
     * Get emails from predefined TypoScript
     *
     *      plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {
     *          1.email = TEXT
     *          1.email.value = email1@domain.org, email2@domain.org
     *      }
     *
     * @param string $predefinedString
     * @return array
     */
    protected function getEmailsFromPredefinedEmail($predefinedString)
    {
        $receiverString = '';
        TypoScriptUtility::overwriteValueFromTypoScript(
            $receiverString,
            $this->configuration['receiver.']['predefinedReceiver.'][$predefinedString . '.'],
            'email'
        );
        return $this->parseEmailsFromString($receiverString);
    }

    /**
     * Get email string from TypoScript overwrite
     *
     * @return array
     */
    protected function overWriteEmailsWithTypoScript()
    {
        $receiverString = '';
        TypoScriptUtility::overwriteValueFromTypoScript(
            $receiverString,
            $this->configuration['receiver.']['overwrite.'],
            'email'
        );
        return $this->parseEmailsFromString($receiverString);
    }

    /**
     * Read emails from String and split it on break, pipe, comma and semicolon
     *
     * @param string $string Any given string from a textarea with some emails
     * @return array Array with emails
     */
    protected function parseEmailsFromString($string)
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
