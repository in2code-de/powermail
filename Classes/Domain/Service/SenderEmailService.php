<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
 * Class SenderEmailService to get email array for sender attributes
 *
 * @package In2code\Powermail\Domain\Service
 */
class SenderEmailService
{
    use SignalTrait;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
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
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * Get sender email from configuration in fields and params. If empty, take default from TypoScript
     * 
     * @return string
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
