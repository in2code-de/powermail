<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use \TYPO3\CMS\Core\Utility\VersionNumberUtility;


/**
 * Backend Check Viewhelper: Check if TYPO3 Version is correct
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class T3VersionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Check if TYPO3 Version is correct
	 *
	 * @return bool
	 */
	public function render() {
		// settings
		$_EXTKEY = 'powermail';
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('powermail') . 'ext_emconf.php');
		$versionString = $EM_CONF['powermail']['constraints']['depends']['typo3'];
		$versions = explode('-', $versionString);
		$powermailVersion = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
		$isAboveMinVersion = ($powermailVersion > VersionNumberUtility::convertVersionNumberToInteger($versions[0]));
		$isBelowMaxVersion = ($powermailVersion < VersionNumberUtility::convertVersionNumberToInteger($versions[1]));
		if ($isAboveMinVersion && $isBelowMaxVersion) {
			return TRUE;
		}
		return FALSE;
	}
}