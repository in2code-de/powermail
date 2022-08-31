<?php
namespace In2code\Powermail\Tests\Helper;

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class ObjectManager
 */
class ObjectManager implements ObjectManagerInterface
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
        return new $objectName;
    }

    /**
     * @param string $objectName
     * @return mixed
     */
    public function getEmptyObject(string $objectName): object
    {
        return $this->get($objectName);
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
