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
    public function isRegistered($objectName)
    {
        unset($objectName);
        return true;
    }

    /**
     * @param string $objectName
     * @return mixed
     */
    public function get($objectName)
    {
        return new $objectName;
    }

    /**
     * @param string $objectName
     * @return mixed
     */
    public function getEmptyObject($objectName)
    {
        return $this->get($objectName);
    }

    /**
     * @return int
     */
    public function getScope($objectName)
    {
        unset($objectName);
        return 0;
    }
}
