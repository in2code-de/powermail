<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetPageNameFromUidViewHelper
 */
class GetPageNameFromUidViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'UID', false, 0);
    }

    public function render(): string
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        return $pageRepository->getPageNameFromUid((int)$this->arguments['uid']);
    }
}
