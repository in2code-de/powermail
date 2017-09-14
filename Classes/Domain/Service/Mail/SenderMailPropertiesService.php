<?php
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TypoScriptUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class SenderMailPropertiesService to get email array for sender attributes
 * for sender emails
 */
class SenderMailPropertiesService
{
    use SignalTrait;

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
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        /** @var TypoScriptService $typoScriptService */
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $this->configuration = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
    }

    /**
     * Get sender email from form settings. If empty, take default from TypoScript or TYPO3 configuration
     *
     * @return string
     */
    public function getSenderEmail()
    {
        if ($this->settings['sender']['email'] !== '') {
            $senderEmail = $this->settings['sender']['email'];
        } else {
            $senderEmail = ConfigurationUtility::getDefaultMailFromAddress();
            TypoScriptUtility::overwriteValueFromTypoScript(
                $senderEmail,
                $this->configuration['sender.']['default.'],
                'senderEmail'
            );
        }

        $signalArguments = [&$senderEmail, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderEmail;
    }

    /**
     * Get sender name from form settings. If empty, take default from TypoScript or TYPO3 configuration.
     *
     * @return string
     */
    public function getSenderName()
    {
        if ($this->settings['sender']['name'] !== '') {
            $senderName = $this->settings['sender']['name'];
        } else {
            $senderName = ConfigurationUtility::getDefaultMailFromName();
            TypoScriptUtility::overwriteValueFromTypoScript(
                $senderName,
                $this->configuration['sender.']['default.'],
                'senderName'
            );
        }

        $signalArguments = [&$senderName, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $senderName;
    }
}
