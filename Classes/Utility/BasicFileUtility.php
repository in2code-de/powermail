<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Exception\FileCannotBeCreatedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Class BasicFileUtility
 */
class BasicFileUtility
{

    /**
     * Get all Files from a folder
     *
     * @param string $path Relative Path
     * @return array
     */
    public static function getFilesFromRelativePath(string $path): array
    {
        $array = [];
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($path));
        foreach ($files as $file) {
            $array[] = $file;
        }
        return $array;
    }

    /**
     * Get path from path and filename
     *
     * @param string $pathAndFilename
     * @return string
     */
    public static function getPathFromPathAndFilename(string $pathAndFilename): string
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['dirname'];
    }

    /**
     * @param string $path
     * @return void
     * @throws FileCannotBeCreatedException
     */
    public static function createFolderIfNotExists(string $path): void
    {
        if (is_dir($path) === false) {
            try {
                GeneralUtility::mkdir_deep($path);
            } catch (\Exception $exception) {
                throw new FileCannotBeCreatedException(
                    'Folder ' . self::getRelativeFolder($path) . ' could not be created',
                    1514817474234
                );
            }
        }
    }

    /**
     * Prepend content to the beginning of a file
     *
     * @param string $pathAndFile
     * @param string $content
     * @return void
     */
    public static function prependContentToFile(string $pathAndFile, string $content): void
    {
        $absolutePathAndFile = GeneralUtility::getFileAbsFileName($pathAndFile);
        $lines = [];
        if (is_file($absolutePathAndFile)) {
            $lines = file($absolutePathAndFile);
        }
        array_unshift($lines, $content);
        GeneralUtility::writeFile($absolutePathAndFile, implode('', $lines));
    }

    /**
     * Get relative path from absolute path, but don't touch if it's already a relative path
     *
     * @param string $path
     * @return string
     */
    public static function getRelativeFolder(string $path): string
    {
        if (PathUtility::isAbsolutePath($path)) {
            $path = PathUtility::getRelativePathTo($path);
        }
        return $path;
    }
}
