<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Service\UploadFolderService;
use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Get Upload Path ViewHelper
 */
class GetHmacForFileViewHelper extends AbstractTagBasedViewHelper
{
    protected string $uploadPathFallback = 'uploads/tx_powermail/';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('fileName', 'string', 'Filename like "picture.jpg"', true);
        $this->registerArgument('path', 'string', 'Path like "fileadmin/powermail/uploads/" or "2:/tx_powermail/"', true);
    }

    public function render(): string
    {
        $fileName = $this->arguments['fileName'] ?? '';
        $path = $this->arguments['path'] ?? $this->uploadPathFallback;

        $service = GeneralUtility::makeInstance(UploadFolderService::class);
        if ($service->isFalCombinedIdentifier((string)$path)) {
            $absFileName = $service->getAbsoluteLocalPath((string)$path, (string)$fileName);
        } else {
            $absFileName = GeneralUtility::getFileAbsFileName($path . $fileName);
        }

        return BasicFileUtility::getHmacForFile($absFileName);
    }
}
