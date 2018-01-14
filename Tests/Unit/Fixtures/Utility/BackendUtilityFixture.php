<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\Utility;

use In2code\Powermail\Utility\BackendUtility;

/**
 * Class BackendUtilityFixture
 */
class BackendUtilityFixture extends BackendUtility
{

    /**
     * @return string
     */
    public static function getModuleNamePublic()
    {
        return self::getModuleName();
    }
}
