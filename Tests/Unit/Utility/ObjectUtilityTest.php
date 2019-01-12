<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Log\Logger;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ObjectUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ObjectUtility
 */
class ObjectUtilityTest extends UnitTestCase
{

    /**
     * @return void
     * @test
     * @covers ::getTyposcriptFrontendController
     * @covers \In2code\Powermail\Utility\AbstractUtility::getTyposcriptFrontendController
     */
    public function getTyposcriptFrontendController()
    {
        TestingHelper::initializeTsfe();
        $result = ObjectUtility::getTyposcriptFrontendController();
        $this->assertInstanceOf(TypoScriptFrontendController::class, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getObjectManager
     * @covers \In2code\Powermail\Utility\AbstractUtility::getObjectManager
     */
    public function getObjectManager()
    {
        $result = ObjectUtility::getObjectManager();
        $this->assertInstanceOf(ObjectManagerInterface::class, $result);
    }

    /**
     * @return void
     * @test
     * @covers ::getContentObject
     */
    public function getContentObject()
    {
        $this->expectExceptionCode(1459422492);
        ObjectUtility::getContentObject();
    }

    /**
     * @return void
     * @test
     * @covers ::getFilesArray
     * @covers \In2code\Powermail\Utility\AbstractUtility::getFilesArray
     */
    public function getFilesArray()
    {
        $result = ObjectUtility::getFilesArray();
        $this->assertTrue(is_array($result));
    }

    /**
     * @return void
     * @test
     * @covers ::getLanguageService
     * @covers \In2code\Powermail\Utility\AbstractUtility::getLanguageService
     */
    public function getLanguageService()
    {
        $result = ObjectUtility::getLanguageService();
        $this->assertNull($result);
    }

    /**
     * @return void
     * @covers ::getLogger
     */
    public function testGetLogger()
    {
        $logger = ObjectUtility::getLogger(__CLASS__);
        $this->assertInstanceOf(Logger::class, $logger);
    }
}
