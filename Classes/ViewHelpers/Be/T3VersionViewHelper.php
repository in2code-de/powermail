<?php
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Backend Check Viewhelper: Check if TYPO3 Version is correct
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class T3VersionViewHelper extends AbstractViewHelper
{

    /**
     * Check if TYPO3 Version is within dependency settings
     *
     * @return bool
     */
    public function render()
    {
        $EM_CONF = [];
        $_EXTKEY = 'powermail';
        require(ExtensionManagementUtility::extPath($_EXTKEY) . 'ext_emconf.php');
        $versionString = $EM_CONF['powermail']['constraints']['depends']['typo3'];
        $versions = explode('-', $versionString);

        return $this->isAboveMinVersion($versions[0]) && $this->isBelowMaxVersion($versions[1]);
    }

    /**
     * Is current TYPO3 newer than the minium allowed version
     *
     * @param string $minTypo3Version
     * @return bool
     */
    protected function isAboveMinVersion($minTypo3Version)
    {
        return $this->getCurrentTypo3Version() >= VersionNumberUtility::convertVersionNumberToInteger($minTypo3Version);
    }

    /**
     * Is current TYPO3 newer than the minium allowed version
     *
     * @param string $maxTypo3Version
     * @return bool
     */
    protected function isBelowMaxVersion($maxTypo3Version)
    {
        return $this->getCurrentTypo3Version() <= VersionNumberUtility::convertVersionNumberToInteger($maxTypo3Version);
    }

    /**
     * Get current TYPO3 version as compareable integer
     *
     * @return int
     */
    protected function getCurrentTypo3Version()
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
    }
}
