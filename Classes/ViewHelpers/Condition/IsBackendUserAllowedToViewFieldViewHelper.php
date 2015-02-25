<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsBackendUserAllowedToViewFieldViewHelper
 *
 * @package In2code\Powermail\ViewHelpers\Condition
 */
class IsBackendUserAllowedToViewFieldViewHelper extends AbstractViewHelper {

	/**
	 * Backend User Object
	 * 
	 * \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected $backendUserAuthentication = NULL;

	/**
	 * Check if Backend User is allowed to see this field
	 *
	 * @param string $table
	 * @param string $field
	 * @return bool
	 */
	public function render($table, $field) {
		return $this->backendUserAuthentication->check('non_exclude_fields', $table . ':' . $field);
	}

	/**
	 * Initialize
	 * 
	 * @return void
	 */
	public function initialize() {
		$this->backendUserAuthentication = $GLOBALS['BE_USER'];
	}

}