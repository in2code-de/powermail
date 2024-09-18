<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Misc;

use In2code\Powermail\ViewHelpers\Misc\VariablesViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class VariablesViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Misc\VariablesViewHelper
 */
class VariablesViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            VariablesViewHelper::class,
            null
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider for removePowermailAllParagraphTagWrapReturnsString()
     *
     * @return array
     */
    public static function removePowermailAllParagraphTagWrapReturnsStringDataProvider(): array
    {
        return [
            [
                '<p class="abc">xyz</p><p>{powermail_all}</p><p class="abc">xyz</p>',
                '<p class="abc">xyz</p>{powermail_all}<p class="abc">xyz</p>',
            ],
            [
                '<p>{powermail_all}</p>',
                '{powermail_all}',
            ],
            [
                '<b>{powermail_all}</b>',
                '<b>{powermail_all}</b>',
            ],
            [
                '<p> {powermail_all} </p>',
                '{powermail_all}',
            ],
            [
                '{powermail_all}',
                '{powermail_all}',
            ],
            [
                '<p class="abc">xyz</p><p>{powermail_all}</p>',
                '<p class="abc">xyz</p>{powermail_all}',
            ],
            [
                '<p>{powermail_all}</p><p class="abc">xyz</p>',
                '{powermail_all}<p class="abc">xyz</p>',
            ],
            [
                '<table><tr><td>{powermail_all}</td></tr></table>',
                '<table><tr><td>{powermail_all}</td></tr></table>',
            ],
            [
                '<table><tr><td><p>	{powermail_all} </p></td></tr></table>',
                '<table><tr><td>{powermail_all}</td></tr></table>',
            ],
        ];
    }

    /**
     * @param string $content
     * @param string $expectedResult
     * @return void
     * @dataProvider removePowermailAllParagraphTagWrapReturnsStringDataProvider
     * @test
     * @covers ::removePowermailAllParagraphTagWrap
     */
    public function removePowermailAllParagraphTagWrapReturnsString($content, $expectedResult)
    {
        $result = $this->abstractValidationViewHelperMock->_call('removePowermailAllParagraphTagWrap', $content);
        self::assertSame($expectedResult, $result);
    }
}
