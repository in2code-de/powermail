<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UploadsFolderViewHelper
 */
class UploadsFolderViewHelper extends AbstractViewHelper
{

    /**
     * Upload Filder
     *
     * @var string
     */
    public $folder = 'uploads/tx_powermail/';

    /**
     * Check if uploads folder exists
     *
     * @return bool
     */
    public function render()
    {
        return file_exists(GeneralUtility::getFileAbsFileName($this->folder));
    }
}
