<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use Throwable;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Get Upload Path ViewHelper
 */
class GetFileWithPathViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * uploadPathFallback
     *
     * @var string
     */
    protected $uploadPathFallback = 'uploads/tx_powermail/';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('fileName', 'string', 'Filename like "picture.jpg"', true);
        $this->registerArgument('path', 'string', 'Path like "fileadmin/powermail/uploads/"', true);
    }

    /**
     * Get path and filename
     */
    public function render(): string
    {
        $fileName = $this->arguments['fileName'] ?? '';
        $path = $this->arguments['path'] ?? '';

        // using FAL although plain path/file is supplied to trigger all hooks and evemts
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $allStorages = $storageRepository->findAll();
        foreach ($allStorages as $thisStorage) {
            try {
                $thisStorageBasePath = $thisStorage->getConfiguration()['basePath'];
                if (str_starts_with((string)$path, (string)$thisStorageBasePath)) {
                    $subPath = substr((string)$path, strlen((string)$thisStorageBasePath));
                    if ($thisStorage->hasFolder($subPath)) {
                        $folder = $thisStorage->getFolder($subPath);
                        $file = $thisStorage->getFileInFolder($fileName, $folder);
                        $filePath = (string)$file->getPublicUrl();
                        return ltrim($filePath, '/');
                    }
                }
            } catch (Throwable $e) {
                unset($e);
            }
        }

        // fallback from FAL storages
        if (file_exists(GeneralUtility::getFileAbsFileName($path . $fileName))) {
            return $path . $fileName;
        }

        return $this->uploadPathFallback . $fileName;
    }
}
