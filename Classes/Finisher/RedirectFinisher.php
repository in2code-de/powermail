<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Service\RedirectUriService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * RedirectFinisher to redirect user after submit
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RedirectFinisher extends AbstractFinisher implements FinisherInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Redirect user after form submit
     *
     * @return void
     */
    public function redirectToUriFinisher()
    {
        /** @var RedirectUriService $redirectService */
        $redirectService = $this->objectManager->get(RedirectUriService::class, $this->contentObject);
        $uri = $redirectService->getRedirectUri();
        if (!empty($uri) && $this->isRedirectEnabled()) {
            HttpUtility::redirect($uri);
        }
    }

    /**
     * @return bool
     */
    protected function isRedirectEnabled()
    {
        return !(!empty($this->settings['main']['optin']) && empty($this->arguments['hash']));
    }

    /**
     * Initialize
     */
    public function initializeFinisher()
    {
        $this->arguments = GeneralUtility::_GP('tx_powermail_pi1');
    }
}
