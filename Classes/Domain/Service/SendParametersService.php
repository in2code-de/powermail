<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Send Parameters Service Class
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SendParametersService
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * mailRepository
     *
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * Powermail SendPost - Send values via curl to a third party software
     *
     * @param Mail $mail
     * @param array $configuration TypoScript Configuration
     * @return void
     */
    public function sendFromConfiguration(Mail $mail, $configuration)
    {
        $contentObject = $this->configurationManager->getContentObject();
        $spConfiguration = $configuration['marketing.']['sendPost.'];

        // switch of if disabled
        $enable = $contentObject->cObjGetSingle($spConfiguration['_enable'], $spConfiguration['_enable.']);
        if (!$enable) {
            return;
        }

        $contentObject->start($this->mailRepository->getVariablesWithMarkersFromMail($mail));
        $parameters = $contentObject->cObjGetSingle($spConfiguration['values'], $spConfiguration['values.']);
        $curlSettings = [
            'url' => $spConfiguration['targetUrl'],
            'params' => $parameters
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curlSettings['url']);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlSettings['params']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
        curl_close($curl);

        if ($spConfiguration['debug']) {
            GeneralUtility::devLog('SendPost Values', 'powermail', 0, $curlSettings);
        }
    }
}
