<?php
declare(strict_types = 1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class UploadDataProcessor
 */
class UploadDataProcessor extends AbstractDataProcessor
{

    /**
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    public function uploadFilesDataProcessor(): void
    {
        $uploadService = ObjectUtility::getObjectManager()->get(UploadService::class);
        $uploadService->uploadAllFiles();
    }
}
