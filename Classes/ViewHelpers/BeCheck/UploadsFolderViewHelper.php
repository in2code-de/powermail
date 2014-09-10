<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Backend Check Viewhelper: Check if uploads folder exists
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class UploadsFolderViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
	public function render() {
		return file_exists(GeneralUtility::getFileAbsFileName($this->folder));
	}
}