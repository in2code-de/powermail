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
    protected bool $resetSingletonInstances = true;

    /**
     * @test
     * @covers ::getFilesArray
     * @covers \In2code\Powermail\Utility\AbstractUtility::getFilesArray
     */
    public function getFilesArray(): void
    {
        $result = ObjectUtility::getFilesArray();
        self::assertTrue(is_array($result));
    }

    /**
     * @covers ::getLogger
     */
    public function testGetLogger(): void
    {
        $logger = ObjectUtility::getLogger(self::class);
        self::assertInstanceOf(Logger::class, $logger);
    }
}
