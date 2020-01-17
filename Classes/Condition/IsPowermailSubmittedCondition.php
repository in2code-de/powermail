<?php
declare(strict_types=1);
namespace In2code\Powermail\Condition;

use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;

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
        $arguments = $this->getArguments();
        return !empty($arguments['action']) && $arguments['action'] === 'create' && !empty($arguments['mail']['form']);
    }

    /**
     * @return array
     */
    protected function getArguments(): array
    {
        return FrontendUtility::getArguments();
    }
}
