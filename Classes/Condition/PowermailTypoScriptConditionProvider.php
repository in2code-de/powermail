<?php
declare(strict_types = 1);
namespace In2code\Powermail\Condition;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * Class PowermailTypoScriptConditionProvider
 */
class PowermailTypoScriptConditionProvider extends AbstractProvider
{
    /**
     * PowermailTypoScriptConditionProvider constructor.
     */
    public function __construct()
    {
        $this->expressionLanguageProviders = [
            PowermailConditionFunctionsProvider::class,
        ];
    }
}
