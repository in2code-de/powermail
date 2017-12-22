<?php
namespace In2code\Powermail\Tests\Utility;

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
     * @test
     * @covers ::createOptinHash
     */
    public function createHashReturnsString()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'abcdef';
        $form = new Form();
        $form->_setProperty('uid', 123);
        $mail = new Mail();
        $mail->_setProperty('uid', 123);
        $mail->_setProperty('pid', 124);
        $mail->setForm($form);

        $result = OptinUtility::createOptinHash($mail);
        $this->assertEquals('cf06c6db71', $result);
        $this->assertTrue(strlen($result) === 10);
    }
}
