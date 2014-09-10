<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Check if file exists
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class FileExistsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Check if file exists
	 *
	 * @param string $file Filename and Folder (relative)
	 * @return bool
	 */
	public function render($file = '') {
		return file_exists(GeneralUtility::getFileAbsFileName($file));
	}
}