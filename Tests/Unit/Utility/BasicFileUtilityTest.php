<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Exception\FileCannotBeCreatedException;
use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class BasicFileUtiltyTest
 * @coversDefaultClass \In2code\Powermail\Utility\BasicFileUtility
 */
class BasicFileUtilityTest extends UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestingHelper::setDefaultConstants();
    }

    /**
     * @test
     * @covers ::getFilesFromRelativePath
     */
    public function getFilesFromRelativePathReturnsString(): void
    {
        $result = BasicFileUtility::getFilesFromRelativePath('typo3/');
        self::assertSame(['index.php', 'install.php'], $result);
    }

    /**
     * @test
     * @covers ::getPathFromPathAndFilename
     */
    public function getPathFromPathAndFilenameReturnsString(): void
    {
        $result = BasicFileUtility::getPathFromPathAndFilename('typo3/index.php');
        self::assertSame('typo3', $result);
    }

    /**
     * @test
     * @covers ::createFolderIfNotExists
     * @throws FileCannotBeCreatedException
     */
    public function createFolderIfNotExistsReturnsVoid(): void
    {
        $testpath = TestingHelper::getWebRoot() . 'fileadmin/';

        BasicFileUtility::createFolderIfNotExists($testpath);
        self::assertDirectoryExists($testpath);
        GeneralUtility::rmdir($testpath);
    }

    /**
     * @test
     * @covers ::prependContentToFile
     * @throws FileCannotBeCreatedException
     */
    public function prependContentToFileReturnsVoid(): void
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
     * @test
     * @covers ::getRelativeFolder
     */
    public function getRelativeFolderReturnsString(): void
    {
        $testPath = 'typo3conf/ext/powermail/';
        self::assertStringEndsWith(
            $testPath,
            BasicFileUtility::getRelativeFolder(TestingHelper::getWebRoot() . $testPath)
        );
        self::assertSame($testPath, BasicFileUtility::getRelativeFolder($testPath));
    }
}
