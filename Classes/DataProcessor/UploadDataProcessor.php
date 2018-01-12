<?php
declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Utility\ObjectUtility;

/**
 * Class UploadDataProcessor
 */
class UploadDataProcessor extends AbstractDataProcessor
{

    /**
     * Upload files
     */
    public function uploadFilesDataProcessor()
    {
        $uploadService = ObjectUtility::getObjectManager()->get(UploadService::class);
        $uploadService->uploadAllFiles();
    }
}
