<?php
namespace In2code\Powermail\Tests\Domain\Service;

use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * RedirectUriService Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RedirectUriServiceTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Tests\Fixtures\Domain\Service\RedirectUriServiceFixture
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->initializeTsfe();
        require_once(dirname(dirname(dirname(__FILE__))) . '/Fixtures/Domain/Service/RedirectUriServiceFixture.php');
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Tests\Fixtures\Domain\Service\RedirectUriServiceFixture',
            array('dummy'),
            array(new ContentObjectRenderer())
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
     * Data Provider for getTargetFromFlexFormReturnString()
     *
     * @return array
     */
    public function getTargetFromFlexFormReturnStringDataProvider()
    {
        return array(
            '234' => array(
                array(
                    'thx' => array(
                        'lDEF' => array(
                            'settings.flexform.thx.redirect' => array(
                                'vDEF' => '234'
                            )
                        )
                    )
                ),
                '234'
            ),
            'test.jpg' => array(
                array(
                    'thx' => array(
                        'lDEF' => array(
                            'settings.flexform.thx.redirect' => array(
                                'vDEF' => 'fileadmin/test.jpg'
                            )
                        )
                    )
                ),
                'fileadmin/test.jpg'
            ),
            'empty' => array(
                array(),
                null
            )
        );
    }

    /**
     * getTargetFromFlexForm Test
     *
     * @param array $flexFormArray
     * @param string $expectedResult
     * @dataProvider getTargetFromFlexFormReturnStringDataProvider
     * @return void
     * @test
     */
    public function getTargetFromFlexFormReturnString($flexFormArray, $expectedResult)
    {
        $this->generalValidatorMock->_set('flexFormFixture', $flexFormArray);
        $this->assertEquals($expectedResult, $this->generalValidatorMock->_call('getTargetFromFlexForm'));
    }

    /**
     * Data Provider for getTargetFromTypoScriptReturnString()
     *
     * @return array
     */
    public function getTargetFromTypoScriptReturnStringDataProvider()
    {
        return array(
            '123' => array(
                array(
                    'redirect' => 'TEXT',
                    'redirect.' => array(
                        'value' => '123'
                    )
                ),
                '123'
            ),
            'file.pdf' => array(
                array(
                    'redirect' => 'COA',
                    'redirect.' => array(
                        '10' => 'TEXT',
                        '10.' => array(
                            'wrap' => 'fileadmin/|',
                            'value' => 'file.pdf'
                        )
                    )
                ),
                'fileadmin/file.pdf'
            ),
            'empty' => array(
                array(),
                null
            ),
        );
    }

    /**
     * getTargetFromTypoScript Test
     *
     * @param array $configuration
     * @param string $expectedResult
     * @dataProvider getTargetFromTypoScriptReturnStringDataProvider
     * @return void
     * @test
     */
    public function getTargetFromTypoScriptReturnString(array $configuration, $expectedResult)
    {
        $this->generalValidatorMock->_set('typoScriptFixture', $configuration);
        $this->assertEquals($expectedResult, $this->generalValidatorMock->_call('getTargetFromTypoScript'));
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
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array(
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject',
            'COA' => 'TYPO3\CMS\Frontend\ContentObject\ContentObjectArrayContentObject'
        );
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
    }
}
