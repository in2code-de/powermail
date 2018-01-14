<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsBackendUserAllowedToViewFieldViewHelper
 */
class IsBackendUserAllowedToViewFieldViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'Tablename', true);
        $this->registerArgument('field', 'string', 'Fieldname', true);
    }

    /**
     * Check if Backend User is allowed to see this field
     *
     * @return bool
     */
    public function render(): bool
    {
        return BackendUtility::getBackendUserAuthentication()->check(
            'non_exclude_fields',
            $this->arguments['table'] . ':' . $this->arguments['field']
        );
    }
}
