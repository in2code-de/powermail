<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Package\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
    protected $version = '';

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
     * @throws DBALException
     * @throws Exception
     */
    public function render(): int
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
     * @throws DBALException
     * @throws Exception
     */
    protected function init(): void
    {
        if ($this->getVersion() === '' || $this->getVersion() === '0') {
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

    protected function isCurrentVersionUnsafeCheck(): bool
    {
        $unsafe = false;
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $row = $queryBuilder
            ->select('review_state')
            ->from(self::TABLE_NAME)
            ->where("extension_key = 'powermail' and version = '" . $this->getVersion() . "'")
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
        if (!empty($row['review_state']) && $row['review_state'] === -1) {
            return true;
        }

        return $unsafe;
    }

    protected function isNewerVersionAvailableCheck(): bool
    {
        $newVersionAvailable = false;
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $row = $queryBuilder
            ->select('version')
            ->from(self::TABLE_NAME)
            ->where("extension_key = 'powermail'")
            ->orderBy('version', 'desc')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
        if (!empty($row['version'])) {
            $newestVersion = VersionNumberUtility::convertVersionNumberToInteger($row['version']);
            $currentVersion = VersionNumberUtility::convertVersionNumberToInteger($this->getVersion());
            if ($currentVersion < $newestVersion) {
                $newVersionAvailable = true;
            }
        }

        return $newVersionAvailable;
    }

    protected function isCurrentVersionInExtensionTableExistingCheck(): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $row = $queryBuilder
            ->select('uid')
            ->from(self::TABLE_NAME)
            ->where("extension_key = 'powermail' and version = '" . $this->getVersion() . "'")
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
        return !empty($row['uid']);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getCheckFromDatabase(): bool
    {
        return $this->checkFromDatabase;
    }

    public function setCheckFromDatabase(bool $checkFromDatabase): void
    {
        $this->checkFromDatabase = $checkFromDatabase;
    }

    public function getExtensionTableExists(): bool
    {
        return $this->extensionTableExists;
    }

    public function setExtensionTableExists(bool $extensionTableExists): void
    {
        $this->extensionTableExists = $extensionTableExists;
    }

    public function getIsCurrentVersionUnsecure(): bool
    {
        return $this->isCurrentVersionUnsecure;
    }

    public function setIsCurrentVersionUnsecure(bool $isCurrentVersionUnsecure): void
    {
        $this->isCurrentVersionUnsecure = $isCurrentVersionUnsecure;
    }

    public function getIsNewerVersionAvailable(): bool
    {
        return $this->isNewerVersionAvailable;
    }

    public function setIsNewerVersionAvailable(bool $isNewerVersionAvailable): void
    {
        $this->isNewerVersionAvailable = $isNewerVersionAvailable;
    }

    public function getCurrentVersionInExtensionTableExists(): bool
    {
        return $this->currentVersionInExtensionTableExists;
    }

    public function setCurrentVersionInExtensionTableExists(bool $currentVersionInExtensionTableExists): void
    {
        $this->currentVersionInExtensionTableExists = $currentVersionInExtensionTableExists;
    }
}
