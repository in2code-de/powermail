<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\Utility;

use In2code\Powermail\Utility\AbstractUtility;

/**
 * Class AbstractUtilityFixture
 */
class AbstractUtilityFixture extends AbstractUtility
{

    /**
     * @return string
     */
    public static function getEncryptionKey(): string
    {
        return parent::getEncryptionKey();
    }
}
