<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * PowermailVersionNoteViewHelper
 */
class PowermailVersionNoteViewHelper extends AbstractViewHelper
{
    const TABLE_NAME = 'tx_extensionmanager_domain_model_extension';
    const EXTENSION_VERSION_NOINFORMATION = 0;
    const EXTENSION_VERSION_OK = 1;
    const EXTENSION_VERSION_SECURITYNOTE = 2;
    const EXTENSION_VERSION_UPDATE = 3;

    /**
     * @var int
     */
    protected $status = self::EXTENSION_VERSION_NOINFORMATION;

    /**
     * Current powermail version
     *
     * @var string
     */
    protected $version = null;

    /**
     * Extension Manager table with all extensions exist
     *
     * @var bool
     */
    protected $extensionTableExists = false;

    /**
     * Is there a newer version of powermail
     *
     * @var bool
     */
    protected $isNewerVersionAvailable = false;

    /**
     * This version is listed in EM table
     *
     * @var bool
     */
    protected $currentVersionInExtensionTableExists = false;

    /**
     * This version is unsecure
     *
     * @var bool
     */
    protected $isCurrentVersionUnsecure = false;

    /**
     * Check in database (disable for testing only)
     *
     * @var bool
     */
    protected $checkFromDatabase = true;

    /**
     * Return powermail version
     *
     * @return int
     */
    public function render()
    {
        $this->init();
        if (!$this->getExtensionTableExists()) {
            return $this->getStatus();
        }
        if ($this->getCurrentVersionInExtensionTableExists()) {
            $this->setStatus(self::EXTENSION_VERSION_OK);
            if ($this->getIsNewerVersionAvailable()) {
                $this->setStatus(self::EXTENSION_VERSION_UPDATE);
            }
            if ($this->getIsCurrentVersionUnsecure()) {
                $this->setStatus(self::EXTENSION_VERSION_SECURITYNOTE);
            }
        }
        return $this->getStatus();
    }

    /**
     * @return void
     */
    protected function init()
    {
        if (!$this->getVersion()) {
            $this->setVersion(ExtensionManagementUtility::getExtensionVersion('powermail'));
        }
        if ($this->getCheckFromDatabase()) {
            $this->setIsCurrentVersionUnsecure($this->isCurrentVersionUnsafeCheck());
            $this->setIsNewerVersionAvailable($this->isNewerVersionAvailableCheck());
            $this->setExtensionTableExists(DatabaseUtility::isTableExisting(self::TABLE_NAME));
            $this->setCurrentVersionInExtensionTableExists(
                $this->isCurrentVersionInExtensionTableExistingCheck()
            );
        }
    }

    /**
     * @return bool
     */
    protected function isCurrentVersionUnsafeCheck(): bool
    {
        $unsafe = false;
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('review_state')
            ->from(self::TABLE_NAME)
            ->where('extension_key = "powermail" and version = "' . $this->getVersion() . '"')
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();
        if (!empty($rows[0]['review_state']) && $rows[0]['review_state'] === -1) {
            $unsafe = true;
        }
        return $unsafe;
    }

    /**
     * @return bool
     */
    protected function isNewerVersionAvailableCheck(): bool
    {
        $newVersionAvailable = false;
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('version')
            ->from(self::TABLE_NAME)
            ->where('extension_key = "powermail"')
            ->orderBy('version', 'desc')
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();
        if (!empty($rows[0]['version'])) {
            $newestVersion = VersionNumberUtility::convertVersionNumberToInteger($rows[0]['version']);
            $currentVersion = VersionNumberUtility::convertVersionNumberToInteger($this->getVersion());
            if ($currentVersion < $newestVersion) {
                $newVersionAvailable = true;
            }
        }
        return $newVersionAvailable;
    }

    /**
     * @return bool
     */
    protected function isCurrentVersionInExtensionTableExistingCheck(): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('uid')
            ->from(self::TABLE_NAME)
            ->where('extension_key = "powermail" and version = "' . $this->getVersion() . '"')
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();
        return !empty($rows[0]['uid']);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return void
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return bool
     */
    public function getCheckFromDatabase()
    {
        return $this->checkFromDatabase;
    }

    /**
     * @param bool $checkFromDatabase
     * @return void
     */
    public function setCheckFromDatabase($checkFromDatabase)
    {
        $this->checkFromDatabase = $checkFromDatabase;
    }

    /**
     * @return bool
     */
    public function getExtensionTableExists()
    {
        return $this->extensionTableExists;
    }

    /**
     * @param bool $extensionTableExists
     * @return void
     */
    public function setExtensionTableExists($extensionTableExists)
    {
        $this->extensionTableExists = $extensionTableExists;
    }

    /**
     * @return bool
     */
    public function getIsCurrentVersionUnsecure()
    {
        return $this->isCurrentVersionUnsecure;
    }

    /**
     * @param bool $isCurrentVersionUnsecure
     * @return void
     */
    public function setIsCurrentVersionUnsecure($isCurrentVersionUnsecure)
    {
        $this->isCurrentVersionUnsecure = $isCurrentVersionUnsecure;
    }

    /**
     * @return bool
     */
    public function getIsNewerVersionAvailable()
    {
        return $this->isNewerVersionAvailable;
    }

    /**
     * @param bool $isNewerVersionAvailable
     * @return void
     */
    public function setIsNewerVersionAvailable($isNewerVersionAvailable)
    {
        $this->isNewerVersionAvailable = $isNewerVersionAvailable;
    }

    /**
     * @return bool
     */
    public function getCurrentVersionInExtensionTableExists()
    {
        return $this->currentVersionInExtensionTableExists;
    }

    /**
     * @param bool $currentVersionInExtensionTableExists
     * @return void
     */
    public function setCurrentVersionInExtensionTableExists($currentVersionInExtensionTableExists)
    {
        $this->currentVersionInExtensionTableExists = $currentVersionInExtensionTableExists;
    }
}
