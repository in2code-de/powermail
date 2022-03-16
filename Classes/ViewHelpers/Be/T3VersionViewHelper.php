<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class T3VersionViewHelper
 */
class T3VersionViewHelper extends AbstractViewHelper
{

    /**
     * Check if TYPO3 Version is within dependency settings
     *
     * @return bool
     */
    public function render(): bool
    {
        if (!Environment::isComposerMode()) {
            $EM_CONF = [];
            require(ExtensionManagementUtility::extPath('powermail') . 'ext_emconf.php');

            $config = current($EM_CONF);
            $versionString = $config['constraints']['depends']['typo3'];

            $versions = explode('-', $versionString);

            return $this->isAboveMinVersion($versions[0]) && $this->isBelowMaxVersion($versions[1]);
        }
        return true;
    }

    /**
     * Is current TYPO3 newer than the minium allowed version
     *
     * @param string $minTypo3Version
     * @return bool
     */
    protected function isAboveMinVersion(string $minTypo3Version): bool
    {
        return $this->getCurrentTypo3Version() >= VersionNumberUtility::convertVersionNumberToInteger($minTypo3Version);
    }

    /**
     * Is current TYPO3 newer than the minium allowed version
     *
     * @param string $maxTypo3Version
     * @return bool
     */
    protected function isBelowMaxVersion(string $maxTypo3Version): bool
    {
        return $this->getCurrentTypo3Version() <= VersionNumberUtility::convertVersionNumberToInteger($maxTypo3Version);
    }

    /**
     * Get current TYPO3 version as compareable integer
     *
     * @return int
     */
    protected function getCurrentTypo3Version(): int
    {
        return VersionNumberUtility::convertVersionNumberToInteger(
            GeneralUtility::makeInstance(Typo3Version::class)->getVersion()
        );
    }
}
