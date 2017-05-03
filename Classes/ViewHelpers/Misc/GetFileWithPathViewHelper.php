<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * Get Upload Path
     *
     * @param string $fileName like picture.jpg
     * @param string $path like fileadmin/powermail/uploads/
     * @return string
     */
    public function render($fileName, $path)
    {
        // using FAL although plain path/file is supplied to trigger all hooks and signals
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $allStorages = $storageRepository->findAll();
        foreach ($allStorages as $thisStorage) {
            $thisStorageBasePath = $thisStorage->getConfiguration()['basePath'];
            if (strpos($path, $thisStorageBasePath) === 0) {
                $subPath = substr($path, strlen($thisStorageBasePath));
                if ($thisStorage->hasFolder($subPath)) {
                    $folder = $thisStorage->getFolder($subPath);
                    $file = $thisStorage->getFileInFolder($fileName, $folder);
                    return $file->getPublicUrl();
                }
            }
        }
        // fallback from FAL storages
        if (file_exists(GeneralUtility::getFileAbsFileName($path . $fileName))) {
            return $path . $fileName;
        }
        return $this->uploadPathFallback . $fileName;
    }
}
