<?php
declare(strict_types=1);
namespace In2code\Powermail\Condition;

use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;

/**
 * Class IsPluginOnCurrentPageCondition
 */
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
    public function matchCondition(array $conditionParameters): bool
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
     * @param string $conditionParameter like "= value1"
     * @return bool
     * @codeCoverageIgnore
     */
    protected function conditionFits(string $conditionParameter): bool
    {
        $listType = ltrim($conditionParameter, ' =');
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where('pid=' . FrontendUtility::getCurrentPageIdentifier() . ' and list_type="' . $listType . '"')
            ->setMaxResults(1)
            ->execute();
        $rows = $result->fetchAll();
        return !empty($rows[0]['uid']);
    }
}
