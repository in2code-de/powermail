<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Utility\BackendUtility;

/**
 * BackendEditLinkViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class BackendEditLinkViewHelper extends AbstractViewHelper {

	/**
	 * Create a link for backend edit
	 *
	 * @param string $tableName
	 * @param int $identifier
	 * @param bool $addReturnUrl
	 * @return string
	 */
	public function render($tableName, $identifier, $addReturnUrl = TRUE) {
		return BackendUtility::createEditUri($tableName, $identifier, $addReturnUrl);
	}
}