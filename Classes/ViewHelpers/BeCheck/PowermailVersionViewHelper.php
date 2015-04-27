<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * PowermailVersionViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class PowermailVersionViewHelper extends AbstractViewHelper {

	/**
	 * Return powermail version
	 *
	 * @return string
	 */
	public function render() {
		return ExtensionManagementUtility::getExtensionVersion('powermail');
	}
}