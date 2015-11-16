<?php
namespace In2code\Powermail\Tests\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
 * PrefillFieldViewHelper Test
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class PrefillFieldViewHelperTest extends UnitTestCase
{

    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            '\In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper',
            array('dummy')
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
     * Dataprovider for getDefaultValueReturnsString()
     *
     * @return array
     */
    public function getDefaultValueReturnsStringDataProvider()
    {
        return array(
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(
                    'field' => array(
                        'marker' => 'abc',
                        '123' => 'ghi'
                    ),
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'abc'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(
                    'field' => array(
                        '123' => 'ghi'
                    ),
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'def'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(
                    'field' => array(
                        '123' => 'ghi'
                    ),
                    'uid123' => 'jkl'
                ),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'ghi'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(
                    'uid123' => 'jkl'
                ),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'jkl'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'mno'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(),
                array(),
                'mno'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker'
                ),
                array(),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'pqr'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno'
                ),
                array(
                    'field' => array(
                        'marker' => '',
                        '123' => 'ghi'
                    ),
                    'marker' => 'def',
                    'uid123' => 'jkl'
                ),
                array(
                    'prefill.' => array(
                        'marker' => 'pqr'
                    )
                ),
                'def'
            ),
            array(
                array(
                    'uid' => 123,
                    'marker' => 'marker',
                ),
                array(),
                array(),
                ''
            ),
        );
    }

    /**
     * Test for getDefaultValue()
     *
     * @param array $fieldValues
     * @param array $piVars
     * @param array $settings
     * @param string $expectedResult
     * @return void
     * @dataProvider getDefaultValueReturnsStringDataProvider
     * @test
     */
    public function getDefaultValueReturnsString($fieldValues, $piVars, $settings, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldValues as $name => $value) {
            $field->_setProperty($name, $value);
        }
        $this->abstractValidationViewHelperMock->_set('cObj', new ContentObjectRenderer());
        $this->abstractValidationViewHelperMock->_set('piVars', $piVars);
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $this->abstractValidationViewHelperMock->_set('field', $field);
        $this->abstractValidationViewHelperMock->_set('marker', $field->getMarker());
        $this->abstractValidationViewHelperMock->_callRef('buildValue');
        $this->assertSame($expectedResult, $this->abstractValidationViewHelperMock->_callRef('getValue'));
    }

    /**
     * Dataprovider for getDefaultValueReturnsString()
     *
     * @return array
     */
    public function getFromTypoScriptContentObjectReturnsStringDataProvider()
    {
        return array(
            array(
                array(
                    'prefill.' => array(
                        'marker' => 'TEXT',
                        'marker.' => array(
                            'value' => 'y',
                            'wrap' => 'x|z'
                        )
                    )
                ),
                'marker',
                'xyz'
            ),
            array(
                array(
                    'prefill.' => array(
                        'email' => 'TEXT',
                        'email.' => array(
                            'data' => 'date:U',
                            'strftime' => '%d.%m.%Y %H:%M'
                        )
                    )
                ),
                'email',
                (string) strftime('%d.%m.%Y %H:%M')
            ),
        );
    }

    /**
     * Test for getFromTypoScriptContentObject()
     *
     * @param array $settings
     * @param string $marker
     * @param string $expectedResult
     * @dataProvider getFromTypoScriptContentObjectReturnsStringDataProvider
     * @test
     */
    public function getFromTypoScriptContentObjectReturnsString(array $settings, $marker, $expectedResult)
    {
        $this->initializeTsfe();
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $field = new Field();
        $field->setMarker($marker);
        $this->abstractValidationViewHelperMock->_set('field', $field);
        $this->abstractValidationViewHelperMock->_set('marker', $marker);
        $this->abstractValidationViewHelperMock->_set('contentObjectRenderer', new ContentObjectRenderer());
        $this->assertSame(
            $expectedResult,
            $this->abstractValidationViewHelperMock->_callRef('getFromTypoScriptContentObject')
        );
    }

    /**
     * Dataprovider for getFromTypoScriptRawReturnsString()
     *
     * @return array
     */
    public function getFromTypoScriptRawReturnsStringDataProvider()
    {
        return array(
            array(
                array(
                    'prefill.' => array(
                        'email' => 'abcdef'
                    )
                ),
                'email',
                'abcdef'
            ),
            array(
                array(
                    'prefill.' => array(
                        'email' => 'TEXT',
                        'email.' => array(
                            'value' => 'xyz'
                        )
                    )
                ),
                'email',
                ''
            ),
            array(
                array(
                    'prefill.' => array(
                        'marker' => 'TEXT'
                    )
                ),
                'marker',
                'TEXT'
            ),
        );
    }

    /**
     * Test for getFromTypoScriptRaw()
     *
     * @param array $settings
     * @param string $marker
     * @param string $expectedResult
     * @dataProvider getFromTypoScriptRawReturnsStringDataProvider
     * @test
     */
    public function getFromTypoScriptRawReturnsString(array $settings, $marker, $expectedResult)
    {
        $this->abstractValidationViewHelperMock->_set('settings', $settings);
        $this->abstractValidationViewHelperMock->_set('marker', $marker);
        $this->assertSame($expectedResult, $this->abstractValidationViewHelperMock->_callRef('getFromTypoScriptRaw'));
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
            'TEXT' => 'TYPO3\CMS\Frontend\ContentObject\TextContentObject'
        );
        $GLOBALS['TT'] = new TimeTracker();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);
    }
}
