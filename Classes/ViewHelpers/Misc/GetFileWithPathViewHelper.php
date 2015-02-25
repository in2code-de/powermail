<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get Upload Path ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetFileWithPathViewHelper extends AbstractViewHelper {

	/**
	 * uploadPathFallback
	 *
	 * @var string
	 */
	protected $uploadPathFallback = 'uploads/tx_powermail/';

	/**
	 * Get Upload Path
	 *
	 * @param string $fileName like picture.jpg
	 * @param string $path like fileadmin/powermail/uploads/
	 * @return string
	 */
	public function render($fileName, $path) {
		if (file_exists(GeneralUtility::getFileAbsFileName($path . $fileName))) {
			return $path . $fileName;
		}
		return $this->uploadPathFallback . $fileName;
	}
}