<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetPageNameFromUidViewHelper
 */
class GetPageNameFromUidViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'UID', false, 0);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        return $pageRepository->getPageNameFromUid($this->arguments['uid']);
    }
}
