<?php
namespace In2code\Powermail\Tests\Unit\Condition;

use In2code\Powermail\Condition\IsPluginOnCurrentPageCondition;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class IsPluginOnCurrentPageConditionTest
 * @coversDefaultClass \In2code\Powermail\Condition\IsPluginOnCurrentPageCondition
 */
class IsPluginOnCurrentPageConditionTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @return void
     * @test
     * @covers ::matchCondition
     */
    public function matchConditionReturnsBool()
    {
        /**
         * @var IsPluginOnCurrentPageCondition|\PHPUnit_Framework_MockObject_MockObject $isCurrentPageWithTrueCondition
         */
        $isCurrentPageWithTrueCondition = $this
            ->getMockBuilder(IsPluginOnCurrentPageCondition::class)
            ->setMethods(['conditionFits'])
            ->getMock();
        $isCurrentPageWithTrueCondition->method('conditionFits')->willReturn(true);
        $this->assertTrue($isCurrentPageWithTrueCondition->matchCondition(['= value1']));
        $this->assertTrue($isCurrentPageWithTrueCondition->matchCondition([]));

        /**
         * @var IsPluginOnCurrentPageCondition|\PHPUnit_Framework_MockObject_MockObject $isCurrentPageWithFalseCondition
         */
        $isCurrentPageWithFalseCondition = $this
            ->getMockBuilder(IsPluginOnCurrentPageCondition::class)
            ->setMethods(['conditionFits'])
            ->getMock();
        $isCurrentPageWithFalseCondition->method('conditionFits')->willReturn(false);
        $this->assertFalse($isCurrentPageWithFalseCondition->matchCondition(['= value1']));
        $this->assertFalse($isCurrentPageWithFalseCondition->matchCondition([]));
    }
}
