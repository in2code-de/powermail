<?php

declare(strict_types=1);

namespace In2code\Powermail\ViewHelpers\Misc;

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
        $this->registerArgument('path', 'string', 'Path like "fileadmin/powermail/uploads/"', true);
    }

    public function render(): string
    {
        $fileName = $this->arguments['fileName'] ?? '';
        $path = $this->arguments['path'] ?? $this->uploadPathFallback;

        $absFileName = GeneralUtility::getFileAbsFileName($path . $fileName);

        return BasicFileUtility::getHmacForFile($absFileName);
    }
}
