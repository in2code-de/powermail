<?php
namespace In2code\Powermail\Tests\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Answer;
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
 * Class LinkMethodTest
 * @package In2code\Powermail\Tests\Domain\Validator\Spamshield
 */
class LinkMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\LinkMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->generalValidatorMock = $this->getAccessibleMock(
            '\In2code\Powermail\Domain\Validator\SpamShield\LinkMethod',
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
            'links allowed 1, 2 links given' => [
                '1',
                'xx <a href="http://www.test.de">http://www.test.de</a> xx',
                true
            ],
            'links allowed 3, 2 links given' => [
                '3',
                'xx <a href="ftp://www.test.de">https://www.test.de</a> xx',
                false
            ],
            'links allowed 0, 1 link given' => [
                '0',
                'xx <a href="#">https://www.test.de</a> xx',
                true
            ],
            'links allowed 2, 3 link given' => [
                '2',
                'xx [url=http://www.xyz.org]http://www.xyz.org[/url] http://www.xyz.org xx',
                true
            ],
        ];
    }

    /**
     * Test for spamCheck()
     *
     * @param int $allowedLinks
     * @param string $text
     * @param bool $expectedResult
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     */
    public function spamCheckReturnsVoid($allowedLinks, $text, $expectedResult)
    {
        $mail = new Mail();
        $answer = new Answer();
        $answer->setValueType(0);
        $answer->setValue($text);
        $mail->addAnswer($answer);

        $this->generalValidatorMock->_set('mail', $mail);
        $this->generalValidatorMock->_set('configuration', ['linkLimit' => $allowedLinks]);
        $this->assertSame($expectedResult, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
