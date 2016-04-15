<?php
namespace In2code\Powermail\Tests\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
 * Class HoneyPodMethodTest
 * @package In2code\Powermail\Tests\Domain\Validator\Spamshield
 */
class HoneyPodMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod',
            ['dummy'],
            [
                new Mail(),
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
     * Dataprovider spamCheckReturnsVoid()
     *
     * @return array
     */
    public function spamCheckReturnsVoidDataProvider()
    {
        return [
            'pot filled 1' => [
                'abc',
                true
            ],
            'pot filled 2' => [
                '@test',
                true
            ],
            'pot empty' => [
                '',
                false
            ],
        ];
    }

    /**
     * Test for spamCheck()
     *
     * @param string $pot if $piVars['field']['__hp'] filled
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     */
    public function spamCheckReturnsVoid($pot, $expectedResult)
    {
        $this->generalValidatorMock->_set('arguments', ['field' => ['__hp' => $pot]]);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
