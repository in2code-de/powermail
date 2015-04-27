<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use In2code\Powermail\Utility\Div;

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
	 * Current powermail version
	 *
	 * @var string
	 */
	protected $version = NULL;

	/**
	 * Extension Manager table with all extensions exist
	 *
	 * @var bool
	 */
	protected $extensionTableExists = FALSE;

	/**
	 * Is there a newer version of powermail
	 *
	 * @var bool
	 */
	protected $isNewerVersionAvailable = FALSE;

	/**
	 * This version is listed in EM table
	 *
	 * @var bool
	 */
	protected $currentVersionInExtensionTableExists = FALSE;

	/**
	 * This version is unsecure
	 *
	 * @var bool
	 */
	protected $isCurrentVersionUnsecure = FALSE;

	/**
	 * Check in database (disable for testing only)
	 *
	 * @var bool
	 */
	protected $checkFromDatabase = TRUE;

	/**
	 * Return powermail version
	 *
	 * @return int
	 */
	public function render() {
		$this->init();
		if (!$this->getExtensionTableExists()) {
			return $this->getStatus();
		}
		if ($this->getCurrentVersionInExtensionTableExists()) {
			$this->setStatus(1);
			if ($this->getIsNewerVersionAvailable()) {
				$this->setStatus(3);
			}
			if ($this->getIsCurrentVersionUnsecure()) {
				$this->setStatus(2);
			}
		}
		return $this->getStatus();
	}

	/**
	 * Database Preflight
	 * @return void
	 */
	protected function init() {
		if (!$this->getVersion()) {
			$this->setVersion(ExtensionManagementUtility::getExtensionVersion('powermail'));
		}
		if ($this->getCheckFromDatabase()) {
			$this->setIsCurrentVersionUnsecure($this->getIsCurrentVersionUnsecureFromDatabase());
			$this->setIsNewerVersionAvailable($this->getIsNewerVersionAvailableFromDatabase());
			$this->setExtensionTableExists($this->getExtensionTableExistsFromDatabase());
			$this->setCurrentVersionInExtensionTableExists($this->getCurrentVersionInExtensionTableExistsFromDatabase());
		}
	}

	/**
	 * @return bool
	 */
	protected function getIsCurrentVersionUnsecureFromDatabase() {
		$select = 'review_state';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail" and version = "' . $this->getVersion() . '"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, '', '', 1);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($row['review_state'] === '0') {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @return bool
	 */
	protected function getIsNewerVersionAvailableFromDatabase() {
		$select = 'version';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail"';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, '', 'version DESC', 1);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if (!empty($row['version'])) {
				$newestVersion = VersionNumberUtility::convertVersionNumberToInteger($row['version']);
				$currentVersion = VersionNumberUtility::convertVersionNumberToInteger($this->getVersion());
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
	protected function getExtensionTableExistsFromDatabase() {
		$allTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		if (array_key_exists('tx_extensionmanager_domain_model_extension', $allTables)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @return bool
	 */
	protected function getCurrentVersionInExtensionTableExistsFromDatabase() {
		$select = 'uid';
		$from = 'tx_extensionmanager_domain_model_extension';
		$where = 'extension_key = "powermail" and version = "' . $this->getVersion() . '"';
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

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @param string $version
	 * @return void
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * @return bool
	 */
	public function getCheckFromDatabase() {
		return $this->checkFromDatabase;
	}

	/**
	 * @param bool $checkFromDatabase
	 * @return void
	 */
	public function setCheckFromDatabase($checkFromDatabase) {
		$this->checkFromDatabase = $checkFromDatabase;
	}

	/**
	 * @return bool
	 */
	public function getExtensionTableExists() {
		return $this->extensionTableExists;
	}

	/**
	 * @param bool $extensionTableExists
	 * @return void
	 */
	public function setExtensionTableExists($extensionTableExists) {
		$this->extensionTableExists = $extensionTableExists;
	}

	/**
	 * @return bool
	 */
	public function getIsCurrentVersionUnsecure() {
		return $this->isCurrentVersionUnsecure;
	}

	/**
	 * @param bool $isCurrentVersionUnsecure
	 * @return void
	 */
	public function setIsCurrentVersionUnsecure($isCurrentVersionUnsecure) {
		$this->isCurrentVersionUnsecure = $isCurrentVersionUnsecure;
	}

	/**
	 * @return bool
	 */
	public function getIsNewerVersionAvailable() {
		return $this->isNewerVersionAvailable;
	}

	/**
	 * @param bool $isNewerVersionAvailable
	 * @return void
	 */
	public function setIsNewerVersionAvailable($isNewerVersionAvailable) {
		$this->isNewerVersionAvailable = $isNewerVersionAvailable;
	}

	/**
	 * @return bool
	 */
	public function getCurrentVersionInExtensionTableExists() {
		return $this->currentVersionInExtensionTableExists;
	}

	/**
	 * @param bool $currentVersionInExtensionTableExists
	 * @return void
	 */
	public function setCurrentVersionInExtensionTableExists($currentVersionInExtensionTableExists) {
		$this->currentVersionInExtensionTableExists = $currentVersionInExtensionTableExists;
	}
}