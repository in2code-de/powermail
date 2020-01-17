<?php
namespace In2code\Powermail\Tests\Unit\Condition;

use In2code\Powermail\Tests\Unit\Fixtures\Condition\IsPowermailSubmittedConditionFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class IsPowermailSubmittedConditionTest
 * @coversDefaultClass \In2code\Powermail\Condition\IsPowermailSubmittedCondition
 */
class IsPowermailSubmittedConditionTest extends UnitTestCase
{

    /**
     * @var array
     */
    protected $testFilesToDelete = [];

    /**
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     * @covers ::matchCondition
     * @covers ::getArguments
     */
    public function testMatchCondition()
    {
        $condition = new IsPowermailSubmittedConditionFixture();
        $this->assertFalse($condition->matchCondition([]));

        $arguments = [
            'tx_powermail_pi1' => [
                'action' => 'create',
                'mail' => [
                    'form' => '123'
                ]
            ]
        ];
        $_POST = $arguments;
        $this->assertTrue($condition->matchCondition([]));
    }
}
