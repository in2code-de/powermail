<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\FrontendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetClassNameOnActionViewHelper
 */
class GetClassNameOnActionViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('actionName', 'string', 'Given action name', true);
        $this->registerArgument('className', 'string', 'Classname to return if action fits', false, ' btn-primary');
        $this->registerArgument('fallbackClassName', 'string', 'Classname for another action', false, ' btn-secondary');
    }

    /**
     * Return className if actionName fits to current action
     */
    public function render(): string
    {
        if ($this->getCurrentActionName() === $this->arguments['actionName']) {
            return $this->arguments['className'];
        }

        return $this->arguments['fallbackClassName'];
    }

    protected function getCurrentActionName(): string
    {
        $actionName = FrontendUtility::getActionName();
        if ($actionName === '') {
            return 'list';
        }

        return $actionName;
    }
}
