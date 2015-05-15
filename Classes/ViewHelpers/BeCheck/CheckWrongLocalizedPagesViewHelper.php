<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CheckWrongLocalizedPagesViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\BeCheck
 */
class CheckWrongLocalizedPagesViewHelper extends AbstractViewHelper {

	/**
	 * pageRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;

	/**
	 * Check if there are localized records with
	 * 		tx_powermail_domain_model_pages.forms = 0
	 *
	 * @return bool
	 */
	public function render() {
		$pages = $this->pageRepository->findAllWrongLocalizedPages();
		if (count($pages) > 0) {
			return FALSE;
		}
		return TRUE;
	}
}