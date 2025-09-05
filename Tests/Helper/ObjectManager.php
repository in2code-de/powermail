<?php

namespace In2code\Powermail\Tests\Helper;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ObjectManager
 */
class ObjectManager
{
    public function isRegistered(string $objectName): bool
    {
        unset($objectName);
        return true;
    }

    public function get(string $objectName, mixed ...$constructorArguments): object
    {
        unset($constructorArguments);
        return new $objectName();
    }

    public function getEmptyObject(string $objectName): object
    {
        return GeneralUtility::makeInstance($objectName);
    }

    public function getScope(string $objectName): int
    {
        unset($objectName);
        return 0;
    }
}
