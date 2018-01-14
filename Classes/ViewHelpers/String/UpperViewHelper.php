<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UpperViewHelper
 */
class UpperViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'Any string', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return ucfirst($this->arguments['string']);
    }
}
