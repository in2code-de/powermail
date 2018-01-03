<?php
namespace In2code\Powermail\Tests\Unit\ViewHelpers\Be;

use In2code\Powermail\ViewHelpers\Be\PowermailVersionNoteViewHelper;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class PowermailVersionNoteViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Be\PowermailVersionNoteViewHelper
 */
class PowermailVersionNoteViewHelperTest extends UnitTestCase
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
            PowermailVersionNoteViewHelper::class,
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
                0
            ],
            [
                true,
                true,
                true,
                false,
                3
            ],
            [
                false,
                true,
                true,
                true,
                0
            ],
            [
                true,
                false,
                true,
                false,
                1
            ],
            [
                true,
                true,
                true,
                true,
                2
            ],
            [
                true,
                false,
                true,
                true,
                2
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
        $this->abstractValidationViewHelperMock->_set('checkFromDatabase', false);
        $this->abstractValidationViewHelperMock->_callRef('setExtensionTableExists', $extensionTableExists);
        $this->abstractValidationViewHelperMock->_callRef('setIsNewerVersionAvailable', $isNewerVersionAvailable);
        $this->abstractValidationViewHelperMock->_callRef(
            'setCurrentVersionInExtensionTableExists',
            $currentVersionInExtensionTableExists
        );
        $this->abstractValidationViewHelperMock->_callRef('setIsCurrentVersionUnsecure', $isCurrentVersionUnsecure);
        $result = $this->abstractValidationViewHelperMock->_callRef('render');
        $this->assertSame($expectedResult, $result);
    }
}
