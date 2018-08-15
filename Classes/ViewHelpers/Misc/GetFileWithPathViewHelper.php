<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get Upload Path ViewHelper
 */
class GetFileWithPathViewHelper extends AbstractViewHelper
{

    /**
     * uploadPathFallback
     *
     * @var string
     */
    protected $uploadPathFallback = 'uploads/tx_powermail/';

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('fileName', 'string', 'Filename like "picture.jpg"', true);
        $this->registerArgument('path', 'string', 'Path like "fileadmin/powermail/uploads/"', true);
    }

    /**
     * Get path and filename
     *
     * @return string
     */
    public function render(): string
    {
        $fileName = $this->arguments['fileName'];
        $path = $this->arguments['path'];

        // using FAL although plain path/file is supplied to trigger all hooks and signals
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $allStorages = $storageRepository->findAll();
        foreach ($allStorages as $thisStorage) {
            try {
                $thisStorageBasePath = $thisStorage->getConfiguration()['basePath'];
                if (strpos($path, $thisStorageBasePath) === 0) {
                    $subPath = substr($path, strlen($thisStorageBasePath));
                    if ($thisStorage->hasFolder($subPath)) {
                        $folder = $thisStorage->getFolder($subPath);
                        $file = $thisStorage->getFileInFolder($fileName, $folder);
                        return (string)$file->getPublicUrl();
                    }
                }
            } catch (\Exception $e) {
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
