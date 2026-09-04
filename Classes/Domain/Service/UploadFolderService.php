<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\File as FalFile;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Resolves the configured upload folder to either a FAL Folder object (when a
 * combined identifier like "2:/tx_powermail/" is configured) or to the legacy
 * relative filesystem path (e.g. "uploads/tx_powermail/").
 *
 * All FAL-aware code paths in powermail route through this service so that the
 * rest of the extension does not need to know which mode is active.
 */
class UploadFolderService implements SingletonInterface
{
    protected ResourceFactory $resourceFactory;

    public function __construct()
    {
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }

    /**
     * Whether the given folder spec is a FAL combined identifier ("<storageUid>:/path")
     */
    public function isFalCombinedIdentifier(string $folder): bool
    {
        if (!str_contains($folder, ':')) {
            return false;
        }
        $parts = GeneralUtility::trimExplode(':', $folder, false, 2);
        return count($parts) === 2 && MathUtility::canBeInterpretedAsInteger($parts[0]);
    }

    /**
     * Returns a FAL Folder for a combined identifier, or null when the spec is
     * a legacy relative path or the folder does not exist.
     */
    public function getFolder(string $folder): ?Folder
    {
        if (!$this->isFalCombinedIdentifier($folder)) {
            return null;
        }
        try {
            return $this->resourceFactory->getFolderObjectFromCombinedIdentifier($folder);
        } catch (FolderDoesNotExistException | InsufficientFolderAccessPermissionsException) {
            return null;
        }
    }

    /**
     * Returns a FAL Folder for a combined identifier (created if missing), or
     * the legacy relative path string when not a combined identifier.
     *
     * @return Folder|string
     * @throws FolderDoesNotExistException when the FAL folder could not be created
     */
    public function getOrCreateFolder(string $folder): Folder|string
    {
        if (!$this->isFalCombinedIdentifier($folder)) {
            return StringUtility::addTrailingSlash($folder);
        }

        try {
            return $this->resourceFactory->getFolderObjectFromCombinedIdentifier($folder);
        } catch (FolderDoesNotExistException) {
            return $this->createFalFolder($folder);
        } catch (InsufficientFolderAccessPermissionsException $e) {
            throw new FolderDoesNotExistException(
                'Upload folder "' . $folder . '" is not accessible and could not be resolved: ' . $e->getMessage(),
                1730000001
            );
        }
    }

    /**
     * Resolve the absolute local filesystem path of a file inside the configured folder.
     *
     * For FAL folders this uses File::getForLocalProcessing() so that even non-public
     * storages (e.g. outside the document root) are supported.
     */
    public function getAbsoluteLocalPath(string $folder, string $fileName): string
    {
        $file = $this->getFalFile($folder, $fileName);
        if ($file !== null) {
            return $file->getForLocalProcessing(false);
        }
        return $this->isFalCombinedIdentifier($folder)
            ? ''
            : GeneralUtility::getFileAbsFileName($folder . $fileName);
    }

    /**
     * Whether a file with the given name exists in the configured folder.
     */
    public function fileExists(string $folder, string $fileName): bool
    {
        if ($this->isFalCombinedIdentifier($folder)) {
            return $this->getFalFile($folder, $fileName) !== null;
        }
        return is_file(GeneralUtility::getFileAbsFileName($folder . $fileName));
    }

    /**
     * Public web-facing path for the file (relative to the site root).
     *
     * For FAL folders this is the File public URL; for legacy folders it is
     * the concatenated relative path (returned even if the file does not exist,
     * so callers can use it for link construction).
     */
    public function getPublicUrl(string $folder, string $fileName): string
    {
        $file = $this->getFalFile($folder, $fileName);
        if ($file !== null) {
            return ltrim((string)$file->getPublicUrl(), '/');
        }
        return $this->isFalCombinedIdentifier($folder) ? '' : $folder . $fileName;
    }

    /**
     * List all file names inside the configured folder.
     *
     * @return string[]
     */
    public function getFileNames(string $folder): array
    {
        $resolvedFolder = $this->getOrCreateFolder($folder);
        if ($resolvedFolder instanceof Folder) {
            $names = [];
            foreach ($resolvedFolder->getFiles() as $file) {
                $names[] = $file->getName();
            }
            return $names;
        }
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($resolvedFolder));
        if (!is_array($files)) {
            return [];
        }
        return array_values($files);
    }

    /**
     * Add an uploaded (temporary) file to the configured folder.
     *
     * For FAL folders this uses Folder::addFile(); for legacy folders it falls back
     * to GeneralUtility::upload_copy_move().
     *
     * @return bool true on success
     */
    public function addUploadedFile(string $folder, string $temporaryName, string $targetFileName): bool
    {
        $resolvedFolder = $this->getOrCreateFolder($folder);
        if ($resolvedFolder instanceof Folder) {
            try {
                $resolvedFolder->addFile($temporaryName, $targetFileName, DuplicationBehavior::RENAME);
                return true;
            } catch (\Throwable) {
                return false;
            }
        }
        $absolutePath = GeneralUtility::getFileAbsFileName($resolvedFolder . $targetFileName);
        return GeneralUtility::upload_copy_move($temporaryName, $absolutePath);
    }

    /**
     * Delete a single file from the configured folder.
     */
    public function deleteFile(string $folder, string $fileName): bool
    {
        $file = $this->getFalFile($folder, $fileName);
        if ($file !== null) {
            return $file->delete();
        }
        if ($this->isFalCombinedIdentifier($folder)) {
            return false;
        }
        $absolutePath = GeneralUtility::getFileAbsFileName($folder . $fileName);
        if ($absolutePath !== '' && is_file($absolutePath)) {
            return unlink($absolutePath);
        }
        return false;
    }

    /**
     * Modification time of a file inside the configured folder, or 0 on failure.
     */
    public function getModificationTime(string $folder, string $fileName): int
    {
        $file = $this->getFalFile($folder, $fileName);
        if ($file !== null) {
            return (int)$file->getProperty('modification_date');
        }
        if ($this->isFalCombinedIdentifier($folder)) {
            return 0;
        }
        $absolutePath = GeneralUtility::getFileAbsFileName($folder . $fileName);
        return $absolutePath !== '' && is_file($absolutePath) ? (int)filemtime($absolutePath) : 0;
    }

    /**
     * Create a (possibly nested) folder in a FAL storage.
     *
     * Uses ResourceStorage::createFolder() which hardcodes recursive creation
     * internally, so a nested path like "tx_powermail/subdir" is created in a
     * single call.
     *
     * @throws FolderDoesNotExistException when the storage is missing or creation fails
     */
    protected function createFalFolder(string $folder): Folder
    {
        $parts = GeneralUtility::trimExplode(':', $folder, false, 2);
        $storageUid = (int)$parts[0];
        $path = trim($parts[1] ?? '', '/');

        try {
            $storage = $this->resourceFactory->getStorageObject($storageUid);
        } catch (\Throwable $e) {
            throw new FolderDoesNotExistException(
                'Could not resolve FAL storage "' . $storageUid . '": ' . $e->getMessage(),
                1730000002
            );
        }

        if ($path === '') {
            try {
                return $storage->getRootLevelFolder();
            } catch (\Throwable $e) {
                throw new FolderDoesNotExistException(
                    'Could not get root level folder of storage "' . $storageUid . '": ' . $e->getMessage(),
                    1730000003
                );
            }
        }

        try {
            return $storage->createFolder($path);
        } catch (\Throwable $e) {
            throw new FolderDoesNotExistException(
                'Could not create folder "' . $path . '" in storage "' . $storageUid . '": ' . $e->getMessage(),
                1730000004
            );
        }
    }

    /**
     * Resolve a FAL File from a folder combined identifier and a file name.
     *
     * Uses ResourceFactory::retrieveFileOrFolderObject() which handles the
     * combined identifier, storage lookup, and file existence check in one call.
     *
     * Returns null for legacy paths or when the file does not exist.
     */
    protected function getFalFile(string $folder, string $fileName): ?FalFile
    {
        if (!$this->isFalCombinedIdentifier($folder)) {
            return null;
        }
        $combinedIdentifier = rtrim($folder, '/') . '/' . ltrim($fileName, '/');
        try {
            $object = $this->resourceFactory->retrieveFileOrFolderObject($combinedIdentifier);
        } catch (\Throwable) {
            return null;
        }
        return $object instanceof FalFile ? $object : null;
    }
}
