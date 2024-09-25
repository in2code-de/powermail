<?php

namespace In2code\Powermail\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class BackendUserGroup extends AbstractEntity
{
    public const FILE_OPPERATIONS = 1;
    public const DIRECTORY_OPPERATIONS = 4;
    public const DIRECTORY_COPY = 8;
    public const DIRECTORY_REMOVE_RECURSIVELY = 16;

    /**
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<BackendUserGroup>
     */
    protected $subGroups;

    /**
     * @var string
     */
    protected $modules = '';

    /**
     * @var string
     */
    protected $tablesListening = '';

    /**
     * @var string
     */
    protected $tablesModify = '';

    /**
     * @var string
     */
    protected $pageTypes = '';

    /**
     * @var string
     */
    protected $allowedExcludeFields = '';

    /**
     * @var string
     */
    protected $explicitlyAllowAndDeny = '';

    /**
     * @var string
     */
    protected $allowedLanguages = '';

    /**
     * @var bool
     */
    protected $workspacePermission = false;

    /**
     * @var string
     */
    protected $databaseMounts = '';

    /**
     * @var int
     */
    protected $fileOperationPermissions = 0;

    /**
     * @var string
     */
    protected $tsConfig = '';

    /**
     * Constructs this backend usergroup
     */
    public function __construct()
    {
        $this->subGroups = new ObjectStorage();
    }

    /**
     * Setter for title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Getter for title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Setter for description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Getter for description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter for the sub groups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $subGroups
     */
    public function setSubGroups(ObjectStorage $subGroups)
    {
        $this->subGroups = $subGroups;
    }

    /**
     * Adds a sub group to this backend user group
     *
     * @param BackendUserGroup $beGroup
     */
    public function addSubGroup(BackendUserGroup $beGroup)
    {
        $this->subGroups->attach($beGroup);
    }

    /**
     * Removes sub group from this backend user group
     *
     * @param BackendUserGroup $groupToDelete
     */
    public function removeSubGroup(BackendUserGroup $groupToDelete)
    {
        $this->subGroups->detach($groupToDelete);
    }

    /**
     * Remove all sub groups from this backend user group
     */
    public function removeAllSubGroups()
    {
        $subGroups = clone $this->subGroups;
        $this->subGroups->removeAll($subGroups);
    }

    /**
     * Getter of sub groups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getSubGroups()
    {
        return $this->subGroups;
    }

    /**
     * Setter for modules
     *
     * @param string $modules
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * Getter for modules
     *
     * @return string
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Setter for tables listening
     *
     * @param string $tablesListening
     */
    public function setTablesListening($tablesListening)
    {
        $this->tablesListening = $tablesListening;
    }

    /**
     * Getter for tables listening
     *
     * @return string
     */
    public function getTablesListening()
    {
        return $this->tablesListening;
    }

    /**
     * Setter for tables modify
     *
     * @param string $tablesModify
     */
    public function setTablesModify($tablesModify)
    {
        $this->tablesModify = $tablesModify;
    }

    /**
     * Getter for tables modify
     *
     * @return string
     */
    public function getTablesModify()
    {
        return $this->tablesModify;
    }

    /**
     * Setter for page types
     *
     * @param string $pageTypes
     */
    public function setPageTypes($pageTypes)
    {
        $this->pageTypes = $pageTypes;
    }

    /**
     * Getter for page types
     *
     * @return string
     */
    public function getPageTypes()
    {
        return $this->pageTypes;
    }

    /**
     * Setter for allowed exclude fields
     *
     * @param string $allowedExcludeFields
     */
    public function setAllowedExcludeFields($allowedExcludeFields)
    {
        $this->allowedExcludeFields = $allowedExcludeFields;
    }

    /**
     * Getter for allowed exclude fields
     *
     * @return string
     */
    public function getAllowedExcludeFields()
    {
        return $this->allowedExcludeFields;
    }

    /**
     * Setter for explicitly allow and deny
     *
     * @param string $explicitlyAllowAndDeny
     */
    public function setExplicitlyAllowAndDeny($explicitlyAllowAndDeny)
    {
        $this->explicitlyAllowAndDeny = $explicitlyAllowAndDeny;
    }

    /**
     * Getter for explicitly allow and deny
     *
     * @return string
     */
    public function getExplicitlyAllowAndDeny()
    {
        return $this->explicitlyAllowAndDeny;
    }

    /**
     * Setter for allowed languages
     *
     * @param string $allowedLanguages
     */
    public function setAllowedLanguages($allowedLanguages)
    {
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * Getter for allowed languages
     *
     * @return string
     */
    public function getAllowedLanguages()
    {
        return $this->allowedLanguages;
    }

    /**
     * Setter for workspace permission
     *
     * @param bool $workspacePermission
     */
    public function setWorkspacePermissions($workspacePermission)
    {
        $this->workspacePermission = $workspacePermission;
    }

    /**
     * Getter for workspace permission
     *
     * @return bool
     */
    public function getWorkspacePermission()
    {
        return $this->workspacePermission;
    }

    /**
     * Setter for database mounts
     *
     * @param string $databaseMounts
     */
    public function setDatabaseMounts($databaseMounts)
    {
        $this->databaseMounts = $databaseMounts;
    }

    /**
     * Getter for database mounts
     *
     * @return string
     */
    public function getDatabaseMounts()
    {
        return $this->databaseMounts;
    }

    /**
     * Getter for file operation permissions
     *
     * @param int $fileOperationPermissions
     */
    public function setFileOperationPermissions($fileOperationPermissions)
    {
        $this->fileOperationPermissions = $fileOperationPermissions;
    }

    /**
     * Getter for file operation permissions
     *
     * @return int
     */
    public function getFileOperationPermissions()
    {
        return $this->fileOperationPermissions;
    }

    /**
     * Check if file operations like upload, copy, move, delete, rename, new and
     * edit files is allowed.
     *
     * @return bool
     */
    public function isFileOperationAllowed()
    {
        return $this->isPermissionSet(self::FILE_OPPERATIONS);
    }

    /**
     * Set the the bit for file operations are allowed.
     *
     * @param bool $value
     */
    public function setFileOperationAllowed($value)
    {
        $this->setPermission(self::FILE_OPPERATIONS, $value);
    }

    /**
     * Check if folder operations like move, delete, rename, and new are allowed.
     *
     * @return bool
     */
    public function isDirectoryOperationAllowed()
    {
        return $this->isPermissionSet(self::DIRECTORY_OPPERATIONS);
    }

    /**
     * Set the the bit for directory operations are allowed.
     *
     * @param bool $value
     */
    public function setDirectoryOperationAllowed($value)
    {
        $this->setPermission(self::DIRECTORY_OPPERATIONS, $value);
    }

    /**
     * Check if it is allowed to copy folders.
     *
     * @return bool
     */
    public function isDirectoryCopyAllowed()
    {
        return $this->isPermissionSet(self::DIRECTORY_COPY);
    }

    /**
     * Set the the bit for copy directories.
     *
     * @param bool $value
     */
    public function setDirectoryCopyAllowed($value)
    {
        $this->setPermission(self::DIRECTORY_COPY, $value);
    }

    /**
     * Check if it is allowed to remove folders recursively.
     *
     * @return bool
     */
    public function isDirectoryRemoveRecursivelyAllowed()
    {
        return $this->isPermissionSet(self::DIRECTORY_REMOVE_RECURSIVELY);
    }

    /**
     * Set the the bit for remove directories recursively.
     *
     * @param bool $value
     */
    public function setDirectoryRemoveRecursivelyAllowed($value)
    {
        $this->setPermission(self::DIRECTORY_REMOVE_RECURSIVELY, $value);
    }

    /**
     * Setter for ts config
     *
     * @param string $tsConfig
     */
    public function setTsConfig($tsConfig)
    {
        $this->tsConfig = $tsConfig;
    }

    /**
     * Getter for ts config
     *
     * @return string
     */
    public function getTsConfig()
    {
        return $this->tsConfig;
    }

    /**
     * Helper method for checking the permissions bitwise.
     *
     * @param int $permission
     * @return bool
     */
    protected function isPermissionSet($permission)
    {
        return ($this->fileOperationPermissions & $permission) == $permission;
    }

    /**
     * Helper method for setting permissions bitwise.
     *
     * @param int $permission
     * @param bool $value
     */
    protected function setPermission($permission, $value)
    {
        if ($value) {
            $this->fileOperationPermissions |= $permission;
        } else {
            $this->fileOperationPermissions &= ~$permission;
        }
    }
}
