<?php
namespace In2code\Powermail\Tests\Helper;

/**
 * Class TestingHelper
 */
class TestingHelper
{

    /**
     * @return void
     */
    public static function setDefaultConstants()
    {
        if (!defined('TYPO3_OS')) {
            define('TYPO3_OS', 'LINUX');
        }
        if (!defined('PATH_site')) {
            define('PATH_site', self::getWebRoot());
        }
        if (!defined('PATH_thisScript')) {
            define('PATH_thisScript', self::getWebRoot() . 'typo3');
        }
        $GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] = '';
    }

    /**
     * @return string
     */
    public static function getWebRoot(): string
    {
        return realpath(__DIR__ . '/../../.Build/Web') . '/';
    }
}
