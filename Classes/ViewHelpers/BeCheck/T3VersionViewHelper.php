<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Backend Check Viewhelper: Check if TYPO3 Version is correct
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class T3VersionViewHelper extends AbstractViewHelper {

	/**
	 * Check if TYPO3 Version is in depends
	 *
	 * @return bool
	 */
	public function render() {
		// settings
		$_EXTKEY = 'powermail';
		require(ExtensionManagementUtility::extPath('powermail') . 'ext_emconf.php');
		$versionString = $EM_CONF['powermail']['constraints']['depends']['typo3'];
		$versions = explode('-', $versionString);
		$typo3Version = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
		$isAboveMinVersion = ($typo3Version > VersionNumberUtility::convertVersionNumberToInteger($versions[0]));
		$isBelowMaxVersion = ($typo3Version < VersionNumberUtility::convertVersionNumberToInteger($versions[1]));
		if ($isAboveMinVersion && $isBelowMaxVersion) {
			return TRUE;
		}
		return FALSE;
	}
}