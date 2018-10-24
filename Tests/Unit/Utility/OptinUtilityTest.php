<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\HashUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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
        $this->assertEquals('c7ff4c2bf7', $result);

        $result = HashUtility::getHash($this->getDummyMail(), 'foo');
        $this->assertEquals('d9829bb000', $result);

        $this->assertTrue(strlen($result) === 10);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::isHashValid
     */
    public function checkOptinHashReturnsBool()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $this->assertFalse(HashUtility::isHashValid('abc123', $this->getDummyMail()));
        $this->assertTrue(HashUtility::isHashValid('c7ff4c2bf7', $this->getDummyMail()));
        $this->assertTrue(HashUtility::isHashValid('d9829bb000', $this->getDummyMail(), 'foo'));
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
