<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use \In2code\Powermail\Utility\Div,
	\TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper,
	\TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * PowermailVersionNoteViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class PowermailVersionNoteViewHelper extends AbstractViewHelper {

	/**
	 * Status of powermail version
	 * 		0 => no version information available
	 * 		1 => ok (no security note, no update)
	 * 		2 => security note
	 * 		3 => no security note but update available
	 *
	 * @var int
	 */
	protected $status = 0;

	/**
	 * Return powermail version
	 *
	 * @return int
	 */
	public function render() {
		if (!$this->extensionTableExists()) {
			return $this->getStatus();
		}
		if ($this->isVersionInTable()) {
			$this->setStatus(1);
			if ($this->isNewerVersionAvailable()) {
				$this->setStatus(3);
			}
			if (!$this->isCurrentVersionSecure()) {
				$this->setStatus(2);
			}
		}
		return $this->getStatus();
	}

	/**
	 * @return bool
	 */
	protected function isNewerVersionAvailable() {
		$select = 'version';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, '', 'version DESC', 1);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if (!empty($row['version'])) {
				$newestVersion = VersionNumberUtility::convertVersionNumberToInteger($row['version']);
				$currentVersion = VersionNumberUtility::convertVersionNumberToInteger(Div::getVersion());
				if ($currentVersion < $newestVersion) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * @return bool
	 */
	protected function isVersionInTable() {
		$select = 'uid';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail" and version = "' . Div::getVersion() . '"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, '', '', 1);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if (!empty($row['uid'])) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return bool
	 */
	protected function isCurrentVersionSecure() {
		$select = 'review_state';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail" and version = "' . Div::getVersion() . '"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, '', '', 1);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row['review_state'] === '0') {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return bool
	 */
	protected function extensionTableExists() {
		$allTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		if (array_key_exists('tx_extensionmanager_domain_model_extension', $allTables)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}
}