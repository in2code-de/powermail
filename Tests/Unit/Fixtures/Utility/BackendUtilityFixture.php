<?php
namespace In2code\Powermail\Unit\Tests\Fixtures\Utility;

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
