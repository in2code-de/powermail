<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Domain\Repository\PageRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CheckWrongLocalizedPagesViewHelper
 */
class CheckWrongLocalizedPagesViewHelper extends AbstractViewHelper
{

    /**
     * Check if there are localized records with
     *        tx_powermail_domain_model_page.forms = 0
     *
     * @return bool
     */
    public function render(): bool
    {
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        $pages = $pageRepository->findAllWrongLocalizedPages();
        return count($pages) === 0;
    }
}
