<?php
declare(strict_types = 1);
namespace In2code\Powermail\Condition;

use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\FrontendUtility;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Class PowermailConditionFunctionsProvider
 * to provide new functions in TypoScript conditions
 */
class PowermailConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @return array|ExpressionFunction[]
     */
    public function getFunctions()
    {
        return [
            $this->isPowermailPluginOnCurrentPageFunction(),
            $this->isPowermailSubmittedFunction(),
        ];
    }

    /**
     * Check if pluginname is anywhere on this page with a new function for conditions: isPowermailOnCurrentPage()
     *
     * Example usages:
     *      [isPowermailOnCurrentPage()] for tt_content.list_type=powermail_pi1 or
     *      [isPowermailOnCurrentPage(['powermail_pi1', 'powermail_pi1'])] for both plugins
     *
     * @return ExpressionFunction
     */
    protected function isPowermailPluginOnCurrentPageFunction(): ExpressionFunction
    {
        return new ExpressionFunction('isPowermailOnCurrentPage', function () {
            // Not implemented, we only use the evaluator
        }, function (array $existingVariables, array $plugins = ['powermail_pi1']) {
            unset($existingVariables);
            return $this->isPluginExistingOnCurrentPageInCurrentLanguage($plugins);
        });
    }

    /**
     * Check if powermail form was just submitted - with a new function: isPowermailSubmitted()
     *
     * Example usage:
     *      [isPowermailSubmitted()]
     *
     * @return ExpressionFunction
     */
    protected function isPowermailSubmittedFunction(): ExpressionFunction
    {
        return new ExpressionFunction('isPowermailSubmitted', function () {
            // Not implemented, we only use the evaluator
        }, function (array $existingVariables) {
            unset($existingVariables);
            $arguments = FrontendUtility::getArguments();
            return !empty($arguments['action']) && $arguments['action'] === 'create'
                && !empty($arguments['mail']['form']);
        });
    }

    /**
     * @param array $plugins like ['powermail_pi1', 'powermail_pi1']
     * @return bool
     */
    protected function isPluginExistingOnCurrentPageInCurrentLanguage(array $plugins): bool
    {
        $listTypes = implode('","', $plugins);
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('tt_content');
        $row = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where('pid=' . FrontendUtility::getCurrentPageIdentifier()
                . ' and CType="list" and list_type in ("' . $listTypes . '") and sys_language_uid='
                . FrontendUtility::getSysLanguageUid())
            ->setMaxResults(1)
            ->execute()
            ->fetch();
        return !empty($row['uid']);
    }
}
