<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Be;

use In2code\Powermail\ViewHelpers\Be\PowermailVersionNoteViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class PowermailVersionNoteViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Be\PowermailVersionNoteViewHelper
 */
class PowermailVersionNoteViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface|PowermailVersionNoteViewHelper
     */
    protected $powermailVersionNoteViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->powermailVersionNoteViewHelperMock = $this->getAccessibleMock(
            PowermailVersionNoteViewHelper::class,
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
     * Dataprovider for renderReturnsInt()
     *
     * @return array
     */
    public static function renderReturnsIntDataProvider(): array
    {
        return [
            [
                false,
                false,
                false,
                false,
                0,
            ],
            [
                true,
                true,
                true,
                false,
                3,
            ],
            [
                false,
                true,
                true,
                true,
                0,
            ],
            [
                true,
                false,
                true,
                false,
                1,
            ],
            [
                true,
                true,
                true,
                true,
                2,
            ],
            [
                true,
                false,
                true,
                true,
                2,
            ],
        ];
    }

    /**
     * @param bool $extensionTableExists
     * @param bool $isNewerVersionAvailable
     * @param bool $currentVersionInExtensionTableExists
     * @param bool $isCurrentVersionUnsecure
     * @param int $expectedResult
     * @return void
     * @dataProvider renderReturnsIntDataProvider
     * @test
     * @covers ::render
     */
    public function renderReturnsInt(
        $extensionTableExists,
        $isNewerVersionAvailable,
        $currentVersionInExtensionTableExists,
        $isCurrentVersionUnsecure,
        $expectedResult
    ) {
        $this->powermailVersionNoteViewHelperMock->setVersion('1.0.0');
        $this->powermailVersionNoteViewHelperMock->_set('checkFromDatabase', false);
        $this->powermailVersionNoteViewHelperMock->_call('setExtensionTableExists', $extensionTableExists);
        $this->powermailVersionNoteViewHelperMock->_call('setIsNewerVersionAvailable', $isNewerVersionAvailable);
        $this->powermailVersionNoteViewHelperMock->_call(
            'setCurrentVersionInExtensionTableExists',
            $currentVersionInExtensionTableExists
        );
        $this->powermailVersionNoteViewHelperMock->_call('setIsCurrentVersionUnsecure', $isCurrentVersionUnsecure);
        $result = $this->powermailVersionNoteViewHelperMock->_call('render');
        self::assertSame($expectedResult, $result);
    }
}
