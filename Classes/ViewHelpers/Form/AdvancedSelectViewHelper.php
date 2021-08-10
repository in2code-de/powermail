<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper;

/**
 * Class AdvancedSelectViewHelper
 */
class AdvancedSelectViewHelper extends SelectViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('defaultOption', 'string', 'value to prepend', false);
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return ['' => $this->arguments['defaultOption']] + parent::getOptions();
    }
}
