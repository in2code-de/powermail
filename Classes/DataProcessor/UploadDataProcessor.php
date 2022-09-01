<?php

declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use Exception;
use In2code\Powermail\Domain\Service\UploadService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UploadDataProcessor
 */
class UploadDataProcessor extends AbstractDataProcessor
{
    /**
     * @return void
     * @throws Exception
     */
    public function uploadFilesDataProcessor(): void
    {
        $uploadService = GeneralUtility::makeInstance(UploadService::class);
        $uploadService->uploadAllFiles();
    }
}
