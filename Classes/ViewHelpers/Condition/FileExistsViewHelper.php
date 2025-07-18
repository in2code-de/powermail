<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class FileExistsViewHelper
 */
class FileExistsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'string', 'Relative path to a file', true);
    }

    public function render(): bool
    {
        return file_exists(GeneralUtility::getFileAbsFileName($this->arguments['file']));
    }
}
