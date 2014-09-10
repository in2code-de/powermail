<?php
namespace In2code\Powermail\ViewHelpers\Condition;

/**
 * OrViewHelper
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class OrViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * OR viewhelper for if widget in fluid
	 *
	 * @param \array $array Array with strings
	 * @param \string $string String to compare
	 * @return \boolean
	 */
	public function render($array, $string = NULL) {
		foreach ((array) $array as $value) {
			if (!$string && $value) {
				return TRUE;
			}
			if ($string && $value == $string) {
				return TRUE;
			}
		}
		return FALSE;
	}
}