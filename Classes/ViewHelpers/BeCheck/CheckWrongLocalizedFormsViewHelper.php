<?php
namespace In2code\Powermail\ViewHelpers\BeCheck;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Backend Check Viewhelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CheckWrongLocalizedFormsViewHelper extends AbstractViewHelper {

	/**
	 * formRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FormRepository
	 * @inject
	 */
	protected $formRepository;

	/**
	 * Check if there are localized records with
	 * 		tx_powermail_domain_model_forms.pages = ""
	 *
	 * @return bool
	 */
	public function render() {
		$forms = $this->formRepository->findAllWrongLocalizedForms();
		if (count($forms) > 0) {
			return FALSE;
		}
		return TRUE;
	}
}