<?php
namespace In2code\Powermail\ViewHelpers\Getter;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Domain\Model\Form;

/**
 * Get Pages with contents which are related to a tt_content-powermail-plugin
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetPagesWithContentRelatedToFormViewHelper extends AbstractViewHelper {

	/**
	 * pageRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;

	/**
	 * Get Pages with contents which are related to a tt_content-powermail-plugin
	 *
	 * @param Form $form
	 * @return array
	 */
	public function render(Form $form) {
		return $this->pageRepository->getPagesWithContentRelatedToForm($form);
	}

}