<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\HashUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class OptinUtilityTest
 *
 * @coversDefaultClass \In2code\Powermail\Utility\HashUtility
 */
class OptinUtilityTest extends UnitTestCase
{
    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::getHash
     * @covers ::createHashFromMail
     * @covers \In2code\Powermail\Utility\AbstractUtility::getEncryptionKey
     * @throws \Exception
     */
    public function createHashReturnsString()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $result = HashUtility::getHash($this->getDummyMail());
        self::assertEquals('8ac41a60329743651a7ffe42c30953e4d67ab1653bc27e994c493a2937c02a2c', $result);

        $result = HashUtility::getHash($this->getDummyMail(), 'foo');
        self::assertEquals('dfb508443aa73e0fbf166c1b006f5c2ca7fc2cce213df1de33708dd00f1b3af4', $result);

        self::assertTrue(strlen($result) === 64);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isHashValid
     * @throws \Exception
     */
    public function checkOptinHashReturnsBool()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        self::assertFalse(HashUtility::isHashValid('abc123', $this->getDummyMail()));
        self::assertTrue(
            HashUtility::isHashValid(
                '8ac41a60329743651a7ffe42c30953e4d67ab1653bc27e994c493a2937c02a2c',
                $this->getDummyMail()
            )
        );
        self::assertTrue(
            HashUtility::isHashValid(
                'dfb508443aa73e0fbf166c1b006f5c2ca7fc2cce213df1de33708dd00f1b3af4',
                $this->getDummyMail(),
                'foo'
            )
        );
    }

    /**
     * @return Mail
     */
    protected function getDummyMail()
    {
        $form = new Form();
        $form->_setProperty('uid', 123);
        $mail = new Mail();
        $mail->_setProperty('uid', 123);
        $mail->_setProperty('pid', 124);
        $mail->setForm($form);
        return $mail;
    }
}
