<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExplodeViewHelper
 */
class ExplodeViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'Any list (e.g. "a,b,c,d")', false, '');
        $this->registerArgument('separator', 'string', 'Separator sign (e.g. ",")', false, ',');
        $this->registerArgument('trim', 'bool', 'Should be trimmed?', false, true);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        return GeneralUtility::trimExplode(
            $this->arguments['separator'],
            $this->arguments['string'],
            $this->arguments['trim']
        );
    }
}
