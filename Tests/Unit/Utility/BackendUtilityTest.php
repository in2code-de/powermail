<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * BackendUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class BackendUtilityTest extends UnitTestCase
{

    /**
     * Data Provider for isBackendAdminReturnsBool()
     *
     * @return array
     */
    public function isBackendAdminReturnsBoolDataProvider()
    {
        return array(
            array(
                1,
                true
            ),
            array(
                0,
                false
            ),
        );
    }

    /**
     * isBackendAdmin Test
     *
     * @param string $value
     * @param bool $expectedResult
     * @dataProvider isBackendAdminReturnsBoolDataProvider
     * @return void
     * @test
     */
    public function isBackendAdminReturnsBool($value, $expectedResult)
    {
        $GLOBALS['BE_USER']->user['admin'] = $value;
        $this->assertSame($expectedResult, BackendUtility::isBackendAdmin());
    }

    /**
     * Data Provider for getPropertyFromBackendUserReturnsString()
     *
     * @return array
     */
    public function getPropertyFromBackendUserReturnsStringDataProvider()
    {
        return array(
            array(
                'admin',
                '1',
            ),
            array(
                'warningMax',
                3
            ),
        );
    }

    /**
     * getPropertyFromBackendUser Test
     *
     * @param string $property
     * @param mixed $value
     * @dataProvider getPropertyFromBackendUserReturnsStringDataProvider
     * @return void
     * @test
     */
    public function getPropertyFromBackendUserReturnsString($property, $value)
    {
        $GLOBALS['BE_USER']->user[$property] = $value;
        $this->assertSame($value, BackendUtility::getPropertyFromBackendUser($property));
    }

    /**
     * Data Provider for getCurrentParametersReturnsArray()
     *
     * @return array
     */
    public function getCurrentParametersReturnsArrayDataProvider()
    {
        return array(
            array(
                array('a' => 'b', 'c' => 'd', 'e' => 'f'),
                array('a' => 'b', 'c' => 'd', 'e' => 'f')
            ),
            array(
                array('a' => 'b', 'c' => 'd', 'M' => 'f'),
                array('a' => 'b', 'c' => 'd')
            ),
            array(
                array('a' => 'b', 'moduleToken' => 'd', 'M' => 'f'),
                array('a' => 'b')
            ),
        );
    }

    /**
     * getCurrentParameters Test
     *
     * @param array $getParameters
     * @param array $expectedResult
     * @dataProvider getCurrentParametersReturnsArrayDataProvider
     * @return void
     * @test
     */
    public function getCurrentParametersReturnsArray($getParameters, $expectedResult)
    {
        $this->assertSame($expectedResult, BackendUtility::getCurrentParameters($getParameters));
    }

    /**
     * Data Provider for getPidFromBackendPageReturnsInt()
     *
     * @return array
     */
    public function getPidFromBackendPageReturnsIntDataProvider()
    {
        return array(
            'TYPO3 6.2 returnUrl' => array(
                '/typo3/sysext/cms/layout/db_layout.php?' .
                'id=17#element-tt_content-14&edit[tt_content][14]=edit',
                17
            ),
            'TYPO3 6.2 returnUrl II' => array(
                '/typo3/sysext/cms/layout/db_layout.php?id=15#element-tt_content-34',
                15
            ),
            'TYPO3 7.6 returnUrl' => array(
                '/typo3/index.php?M=web_layout&moduleToken=' .
                'afcd9cc86e6cd393edac6a60c33f38f2c2b48721&id=15#element-tt_content-34',
                15
            ),
            'Any example' => array(
                '&returnUrl=abc.html?id=1243&abc=123',
                1243
            ),
            'Any example II' => array(
                '&returnUrl=abc.html?abc=1243&xyz=abc',
                0
            ),
        );
    }

    /**
     * getPidFromBackendPage Test
     *
     * @param string $returnUrl
     * @param int $expectedResult
     * @dataProvider getPidFromBackendPageReturnsIntDataProvider
     * @return void
     * @test
     */
    public function getPidFromBackendPageReturnsInt($returnUrl, $expectedResult)
    {
        $this->assertSame($expectedResult, BackendUtility::getPidFromBackendPage($returnUrl));
    }
}
