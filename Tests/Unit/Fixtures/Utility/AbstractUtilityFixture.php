<?php
namespace In2code\Powermail\Unit\Tests\Fixtures\Utility;

use In2code\Powermail\Utility\AbstractUtility;

/**
 * Class AbstractUtilityFixture
 */
class AbstractUtilityFixture extends AbstractUtility
{

    /**
     * @return string
     */
    public static function getEncryptionKey()
    {
        return parent::getEncryptionKey();
    }
}
