<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileExistsViewHelper
 */
class FileExistsViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'string', 'Relative path to a file', true);
    }

    /**
     * @return bool
     */
    public function render(): bool
    {
        return file_exists(GeneralUtility::getFileAbsFileName($this->arguments['file']));
    }
}
