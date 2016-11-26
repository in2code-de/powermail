<?php
namespace In2code\Powermail\Tests\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod;
use In2code\Powermail\Utility\SessionUtility;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class SessionMethodTest
 * @package In2code\Powermail\Tests\Domain\Validator\Spamshield
 */
class SessionMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\SessionMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->initializeTsfe();
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Validator\SpamShield\SessionMethod',
            ['dummy'],
            [
                new Mail(),
                [],
                []
            ]
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Test for spamCheck()
     *
     * @return void
     * @test
     */
    public function spamCheckReturnsVoid()
    {
        $settings = [
            'spamshield' => [
                'methods' => [
                    [
                        'class' => HoneyPodMethod::class,
                        '_enable' => '1'
                    ],
                ],
                '_enable' => '1'
            ]
        ];
        $form = new Form();
        $form->_setProperty('uid', 123);
        SessionUtility::saveFormStartInSession($settings, $form);

        $mail = new Mail();
        $mail->setForm($form);

        $this->generalValidatorMock->_set('mail', $mail);
        $this->assertSame(true, $this->generalValidatorMock->_callRef('spamCheck'));
    }

    /**
     * Initialize TSFE object
     *
     * @return void
     */
    protected function initializeTsfe()
    {
        $configurationManager = new ConfigurationManager();
        $GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = [
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject',
            'COA' => 'TYPO3\CMS\Frontend\ContentObject\ContentObjectArrayContentObject'
        ];
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
        $GLOBALS['TSFE']->fe_user = new FrontendUserAuthentication();
    }
}
