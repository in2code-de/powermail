<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Model\Form;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * BasicFileUtility Tests
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class BasicFileUtiltyTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Utility\BasicFileUtility
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Utility\BasicFileUtility',
            ['dummy']
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
     * Data Provider for addTrailingSlashReturnString()
     *
     * @return array
     */
    public function addTrailingSlashReturnStringDataProvider()
    {
        return [
            [
                'folder1/folder2',
                'folder1/folder2/'
            ],
            [
                'folder1/folder2/',
                'folder1/folder2/'
            ],
            [
                'folder1',
                'folder1/'
            ],
            [
                'folder1///',
                'folder1/'
            ],
            [
                '/fo/ld/er1//',
                '/fo/ld/er1/'
            ],
        ];
    }

    /**
     * addTrailingSlash Test
     *
     * @param string $string
     * @param string $expectedResult
     * @dataProvider addTrailingSlashReturnStringDataProvider
     * @return void
     * @test
     */
    public function addTrailingSlashReturnString($string, $expectedResult)
    {
        $this->assertSame($expectedResult, BasicFileUtility::addTrailingSlash($string));
    }
}
