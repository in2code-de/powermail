<?php
class Tx_Powermail_Domain_Validator_MandatoryValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = true;

	/**
	 * Validation of given Params
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		$gp = t3lib_div::_GP('tx_powermail_pi1');
		$formUid = $gp['form'];
		$form = $this->formsRepository->findByUid($formUid);
		if (!method_exists($form, 'getPages')) {
			return $this->isValid;
		}

		foreach ($form->getPages() as $page) { // every page in current form
			foreach ($page->getFields() as $field) { // every field in current page

				// if not a mandatory field
				if (!$field->getMandatory()) {
					continue;
				}

				// set error
				if (is_array($params[$field->getUid()])) {
					$empty = 1;
					foreach ($params[$field->getUid()] as $value) {
						if (!empty($value)) {
							$empty = 0;
							break;
						}
					}
					if ($empty) {
						$this->addError('mandatory', $field->getUid());
						$this->isValid = false;
					}
				} else {
					if (empty($params[$field->getUid()])) {
						$this->addError('mandatory', $field->getUid());
						$this->isValid = false;
					}
				}
			}
		}

		return $this->isValid;
  	}

	/**
	 * injectFormsRepository
	 *
	 * @param Tx_Powermail_Domain_Repository_FormsRepository $formsRepository
	 * @return void
	 */
	public function injectFormsRepository(Tx_Powermail_Domain_Repository_FormsRepository $formsRepository) {
		$this->formsRepository = $formsRepository;
	}
}
?>