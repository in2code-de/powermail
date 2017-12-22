<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class BasicFileUtiltyTest
 * @coversDefaultClass \In2code\Powermail\Utility\BasicFileUtility
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
            BasicFileUtility::class,
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
     * @param string $string
     * @param string $expectedResult
     * @dataProvider addTrailingSlashReturnStringDataProvider
     * @return void
     * @test
     * @covers ::addTrailingSlash
     */
    public function addTrailingSlashReturnString($string, $expectedResult)
    {
        $this->assertSame($expectedResult, BasicFileUtility::addTrailingSlash($string));
    }
}
