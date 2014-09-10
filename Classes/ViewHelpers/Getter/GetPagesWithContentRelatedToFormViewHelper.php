<?php
namespace In2code\Powermail\ViewHelpers\Getter;

/**
 * Get Pages with contents which are related to a tt_content-powermail-plugin
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class GetPagesWithContentRelatedToFormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @return array
	 */
	public function render(\In2code\Powermail\Domain\Model\Form $form) {
		return $this->pageRepository->getPagesWithContentRelatedToForm($form);
	}

}