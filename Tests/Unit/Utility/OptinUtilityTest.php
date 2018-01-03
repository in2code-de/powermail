<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\OptinUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class OptinUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\OptinUtility
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
     * @covers ::createOptinHash
     * @covers ::createHash
     * @covers \In2code\Powermail\Utility\AbstractUtility::getEncryptionKey
     */
    public function createHashReturnsString()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $result = OptinUtility::createOptinHash($this->getDummyMail());
        $this->assertEquals('cf06c6db71', $result);
        $this->assertTrue(strlen($result) === 10);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @test
     * @covers ::checkOptinHash
     */
    public function checkOptinHashReturnsBool()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $this->assertFalse(OptinUtility::checkOptinHash('abc123', $this->getDummyMail()));
        $this->assertTrue(OptinUtility::checkOptinHash('cf06c6db71', $this->getDummyMail()));
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
