<?php
namespace In2code\Powermail\ViewHelpers\Misc;

/**
 * Returns Morestep Class if morestep is given
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class MorestepClassViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns CSS class for morestep
	 *
	 * @param boolean $activate Current field
	 * @param string $class Any string for class
	 * @return string Class
	 */
	public function render($activate, $class) {
		if ($activate) {
			return $class;
		}
		return '';
	}
}