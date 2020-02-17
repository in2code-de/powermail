<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Exception\ConfigurationIsMissingException;
use In2code\Powermail\Tests\Unit\Fixtures\Utility\HashUtilityFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class HashUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\HashUtility
 */
class HashUtilityTest extends UnitTestCase
{

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @covers ::getEncryptionKey
     * @throws ConfigurationIsMissingException
     */
    public function testGetEncryptionKey()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $this->assertSame('abcdef', HashUtilityFixture::getEncryptionKeyForTesting());

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = '';
        $this->expectExceptionCode(1514910284796);
        HashUtilityFixture::getEncryptionKeyForTesting();
    }
}
