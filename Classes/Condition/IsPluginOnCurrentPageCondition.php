<?php
namespace In2code\Powermail\Condition;

use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;

class IsPluginOnCurrentPageCondition extends AbstractCondition
{

    /**
     * @var string
     */
    protected $defaultParameter = '= powermail_pi1';

    /**
     * Check if pluginname is anywhere on this page
     * [In2code\Powermail\Condition\IsPluginOnCurrentPageCondition] for pi1 or
     * [In2code\Powermail\Condition\IsPluginOnCurrentPageCondition = powermail_pi1, = powermail_pi2] for any plugins
     *
     * @param array $conditionParameters e.g. array('= value1', '= value2')
     * @return bool
     */
    public function matchCondition(array $conditionParameters)
    {
        if (!empty($conditionParameters)) {
            foreach ($conditionParameters as $conditionParameter) {
                if ($this->conditionFits($conditionParameter)) {
                    return true;
                }
            }
        } else {
            if ($this->conditionFits($this->defaultParameter)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $conditionParameter
     * @return bool
     */
    protected function conditionFits($conditionParameter)
    {
        $listType = ltrim($conditionParameter, ' =');
        $row = (array)ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
            'uid',
            'tt_content',
            'deleted=0 and pid=' . FrontendUtility::getCurrentPageIdentifier() . ' and list_type="' . $listType . '"'
        );
        return !empty($row['uid']);
    }
}
