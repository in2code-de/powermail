<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Be;

use In2code\Powermail\ViewHelpers\Be\PowermailVersionNoteViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

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
            ['dummy']
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
    public function renderReturnsIntDataProvider()
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
        $this->powermailVersionNoteViewHelperMock->_callRef('setExtensionTableExists', $extensionTableExists);
        $this->powermailVersionNoteViewHelperMock->_callRef('setIsNewerVersionAvailable', $isNewerVersionAvailable);
        $this->powermailVersionNoteViewHelperMock->_callRef(
            'setCurrentVersionInExtensionTableExists',
            $currentVersionInExtensionTableExists
        );
        $this->powermailVersionNoteViewHelperMock->_callRef('setIsCurrentVersionUnsecure', $isCurrentVersionUnsecure);
        $result = $this->powermailVersionNoteViewHelperMock->_callRef('render');
        self::assertSame($expectedResult, $result);
    }
}
