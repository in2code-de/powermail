<?php

namespace In2code\Powermail\Tests\Helper;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ObjectManager
 */
class ObjectManager
{
    /**
     * @param string $objectName
     * @return bool
     */
    public function isRegistered(string $objectName): bool
    {
        unset($objectName);
        return true;
    }

    /**
     * @param string $objectName
     * @param mixed ...$constructorArguments
     * @return object
     */
    public function get(string $objectName, ...$constructorArguments): object
    {
        unset($constructorArguments);
        return new $objectName();
    }

    /**
     * @param string $objectName
     * @return mixed
     */
    public function getEmptyObject(string $objectName): object
    {
        return GeneralUtility::makeInstance($objectName);
    }

    /**
     * @param string $objectName
     * @return int
     */
    public function getScope(string $objectName): int
    {
        unset($objectName);
        return 0;
    }
}
