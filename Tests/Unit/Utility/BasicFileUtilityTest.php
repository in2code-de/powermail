<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Exception\FileCannotBeCreatedException;
use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BasicFileUtiltyTest
 * @coversDefaultClass \In2code\Powermail\Utility\BasicFileUtility
 */
class BasicFileUtilityTest extends UnitTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        TestingHelper::setDefaultConstants();
    }

    /**
     * @return void
     * @test
     * @covers ::getFilesFromRelativePath
     */
    public function getFilesFromRelativePathReturnsString()
    {
        $result = BasicFileUtility::getFilesFromRelativePath('typo3/');
        self::assertSame(['index.php', 'install.php'], $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getPathFromPathAndFilename
     */
    public function getPathFromPathAndFilenameReturnsString()
    {
        $result = BasicFileUtility::getPathFromPathAndFilename('typo3/index.php');
        self::assertSame('typo3', $result);
    }

    /**
     * @return void
     * @test
     * @covers ::createFolderIfNotExists
     * @throws FileCannotBeCreatedException
     */
    public function createFolderIfNotExistsReturnsVoid()
    {
        $testpath = TestingHelper::getWebRoot() . 'fileadmin/';

        BasicFileUtility::createFolderIfNotExists($testpath);
        self::assertDirectoryExists($testpath);
        GeneralUtility::rmdir($testpath);
    }

    /**
     * @return void
     * @test
     * @covers ::prependContentToFile
     * @throws FileCannotBeCreatedException
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
        self::assertSame(['defabc'], $content);
    }

    /**
     * @return void
     * @test
     * @covers ::getRelativeFolder
     */
    public function getRelativeFolderReturnsString()
    {
        $testPath = 'typo3conf/ext/powermail/';
        self::assertStringEndsWith(
            $testPath,
            BasicFileUtility::getRelativeFolder(TestingHelper::getWebRoot() . $testPath)
        );
        self::assertSame($testPath, BasicFileUtility::getRelativeFolder($testPath));
    }
}
