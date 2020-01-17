<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\Condition;

use In2code\Powermail\Condition\IsPowermailSubmittedCondition;

/**
 * Class IsPowermailSubmittedConditionFixture
 */
class IsPowermailSubmittedConditionFixture extends IsPowermailSubmittedCondition
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getArguments(): array
    {
        return isset($_POST['tx_powermail_pi1']) ? $_POST['tx_powermail_pi1'] : [];
    }
}
