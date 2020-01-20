<?php
namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Tests\Helper\TestingHelper;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Exception as ExceptionCore;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\Logger;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Object\Container\Container;
use TYPO3\CMS\Extbase\Object\Exception;
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
     * @throws ExceptionCore
     */
    public function getTyposcriptFrontendController()
    {
        TestingHelper::initializeTypoScriptFrontendController();
        $result = ObjectUtility::getTyposcriptFrontendController();
        $this->assertInstanceOf(TypoScriptFrontendController::class, $result);
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
     * @throws ExceptionCore
     */
    public function getLanguageService()
    {
        TestingHelper::initializeTypoScriptFrontendController();
        $this->assertInstanceOf(LanguageService::class, ObjectUtility::getLanguageService());
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
