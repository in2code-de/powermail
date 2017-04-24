<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get Upload Path ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
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
        // using FAL although plain path/file to trigger all hooks and signals
        $resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
        $defaultStorage = $resourceFactory->getDefaultStorage();
        $defaultStorageBasePath = $defaultStorage->getConfiguration()['basePath'];
        // check if file is in default storage
        if (strpos($path,$defaultStorageBasePath) === 0 )
        {
            $subPath =  substr($path, strlen($defaultStorageBasePath));
            $folder = $defaultStorage->getFolder($subPath);
            $file = $defaultStorage->getFileInFolder($fileName, $folder);
            return($file->getPublicUrl());    
        } else {
            // fallback from defaultStorage       
            if (file_exists(GeneralUtility::getFileAbsFileName($path . $fileName))) {
                return $path . $fileName;
            }
            return $this->uploadPathFallback . $fileName;
        }
    }
}
