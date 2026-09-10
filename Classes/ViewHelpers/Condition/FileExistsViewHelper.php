<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Domain\Service\UploadFolderService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class FileExistsViewHelper
 *
 * Checks whether a file exists. The given path can be:
 * - a FAL combined identifier like "2:/tx_powermail/file.jpg"
 * - a legacy relative path like "uploads/tx_powermail/file.jpg"
 */
class FileExistsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'string', 'Relative path to a file or a FAL combined identifier', true);
    }

    public function render(): bool
    {
        $file = (string)$this->arguments['file'];
        $service = GeneralUtility::makeInstance(UploadFolderService::class);
        if ($service->isFalCombinedIdentifier($file)) {
            $parts = GeneralUtility::trimExplode(':/', $file, false, 2);
            $folder = $parts[0] . ':/' . (dirname($parts[1]) === '.' ? '' : dirname($parts[1]));
            $fileName = basename($parts[1]);
            return $service->fileExists($folder, $fileName);
        }
        return file_exists(GeneralUtility::getFileAbsFileName($file));
    }
}
