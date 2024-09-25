<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ObjectUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ObjectUtility
 */
class ObjectUtilityTest extends UnitTestCase
{
    /**
     * @var bool
     */
    protected bool $resetSingletonInstances = true;

    /**
     * @return void
     * @test
     * @covers ::getFilesArray
     * @covers \In2code\Powermail\Utility\AbstractUtility::getFilesArray
     */
    public function getFilesArray()
    {
        $result = ObjectUtility::getFilesArray();
        self::assertTrue(is_array($result));
    }

    /**
     * @return void
     * @covers ::getLogger
     */
    public function testGetLogger()
    {
        $logger = ObjectUtility::getLogger(__CLASS__);
        self::assertInstanceOf(Logger::class, $logger);
    }
}
