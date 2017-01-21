<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\FlashMessagesViewHelper;

/**
 * Class GetFlashMessagesPartialNameViewHelper depending on TYPO3 version
 * Because of a breaking change in TYPO3 Flashmessages are more flexible
 * in TYPO3 8.6 then before. But the old including forces an exception
 * which we want to prevent with two different Partials (one for the old
 * and the other for the new way)
 *
 * All information about the changes:
 * https://github.com/TYPO3/TYPO3.CMS/blob/d881b03b7a61d3ce6376fa5d9b03e57e5763a50e/typo3/sysext/core/Documentation/
 *      Changelog/master/Breaking-78477-RefactoringOfFlashMessageRendering.rst
 */
class GetFlashMessagesPartialNameViewHelper extends AbstractViewHelper
{

    /**
     * @var string
     */
    protected $newPartialName = 'New';

    /**
     * @var string
     */
    protected $oldPartialName = 'Old';

    /**
     * @return string
     */
    public function render()
    {
        $partialName = $this->newPartialName;
        if (is_subclass_of(FlashMessagesViewHelper::class, AbstractTagBasedViewHelper::class)) {
            $partialName = $this->oldPartialName;
        }
        return $partialName;
    }
}
