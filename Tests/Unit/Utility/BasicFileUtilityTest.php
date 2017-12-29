<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        TestingHelper::setDefaultConstants();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * @return void
     * @test
     * @covers ::getFilesFromRelativePath
     */
    public function getFilesFromRelativePathReturnsString()
    {
        $result = BasicFileUtility::getFilesFromRelativePath('typo3/');
        $this->assertSame(['cli_dispatch.phpsh', 'index.php', 'install.php'], $result);
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

    /**
     * @return void
     * @test
     * @covers ::getPathFromPathAndFilename
     */
    public function getPathFromPathAndFilenameReturnsString()
    {
        $result = BasicFileUtility::getPathFromPathAndFilename('typo3/index.php');
        $this->assertSame('typo3', $result);
    }

    /**
     * @return void
     * @test
     * @covers ::createFolderIfNotExists
     */
    public function createFolderIfNotExistsReturnsVoid()
    {
        $testpath = TestingHelper::getWebRoot() . 'fileadmin/';

        BasicFileUtility::createFolderIfNotExists($testpath);
        $this->assertDirectoryExists($testpath);
        GeneralUtility::rmdir($testpath);
    }

    /**
     * @return void
     * @test
     * @covers ::prependContentToFile
     */
    public function prependContentToFileReturnsVoid()
    {
        $testpath = TestingHelper::getWebRoot() . 'fileadmin/';
        BasicFileUtility::createFolderIfNotExists($testpath);
        $fileName = $testpath . 'unittest.txt';

        BasicFileUtility::prependContentToFile($fileName, 'abc');
        BasicFileUtility::prependContentToFile($fileName, 'def');
        $content = file($fileName);
        GeneralUtility::rmdir($testpath, true);
        $this->assertSame(['defabc'], $content);
    }

    /**
     * @return void
     * @test
     * @covers ::getRelativeFolder
     */
    public function getRelativeFolderReturnsString()
    {
        $testPath = 'typo3conf/ext/powermail/';
        $this->assertStringEndsWith(
            $testPath,
            BasicFileUtility::getRelativeFolder(TestingHelper::getWebRoot() . $testPath)
        );
        $this->assertSame($testPath, BasicFileUtility::getRelativeFolder($testPath));
    }
}
