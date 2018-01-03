<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Tests\Unit\Fixtures\Utility\AbstractUtilityFixture;
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
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = '';
        $this->expectExceptionCode(1514910284796);
        AbstractUtilityFixture::getEncryptionKey();
    }
}
