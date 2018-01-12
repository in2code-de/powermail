<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetPagesWithContentRelatedToFormViewHelper
 */
class GetPagesWithContentRelatedToFormViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('form', Form::class, 'Form', true);
    }

    /**
     * Get Pages with contents which are related to a tt_content-powermail-plugin
     *
     * @return array
     */
    public function render(): array
    {
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        return $pageRepository->getPagesWithContentRelatedToForm($this->arguments['form']);
    }
}
