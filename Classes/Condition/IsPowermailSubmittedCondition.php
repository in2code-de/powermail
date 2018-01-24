<?php
declare(strict_types=1);
namespace In2code\Powermail\Condition;

use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IsPowermailSubmittedCondition
 */
class IsPowermailSubmittedCondition extends AbstractCondition
{

    /**
     * Check if powermail form was just submitted
     *
     *      Example usage in TypoScript:
     *          [In2code\Powermail\Condition\IsPowermailSubmittedCondition]
     *          // do something
     *          [end]
     *
     * @param array $conditionParameters
     * @return bool
     */
    public function matchCondition(array $conditionParameters): bool
    {
        unset($conditionParameters);
        $arguments = $this->getArgumentsForPlugin1();
        return !empty($arguments['action']) && $arguments['action'] === 'create' && !empty($arguments['mail']['form']);
    }

    /**
     * @return array
     */
    protected function getArgumentsForPlugin1(): array
    {
        return GeneralUtility::_GPmerged('tx_powermail_pi1');
    }
}
