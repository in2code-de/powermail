<?php
namespace In2code\Powermail\Tests\Utility;

use In2code\Powermail\Unit\Tests\Fixtures\Utility\AbstractUtilityFixture;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class AbstractUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\AbstractUtility
 */
class AbstractUtilityTest extends UnitTestCase
{

    /**
     * @return void
     * @test
     * @covers ::getEncryptionKey
     */
    public function getEncryptionKey()
    {
        require_once(dirname(dirname(__FILE__)) . '/Fixtures/Utility/AbstractUtilityFixture.php');
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = '';
        $this->expectExceptionCode(1514910284796);
        AbstractUtilityFixture::getEncryptionKey();
    }
}
