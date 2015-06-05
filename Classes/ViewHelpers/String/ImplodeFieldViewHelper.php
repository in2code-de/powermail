<?php
namespace In2code\Powermail\ViewHelpers\String;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to implode an array or objects to a list
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class ImplodeFieldViewHelper extends AbstractViewHelper {

	/**
	 * View helper to implode an array or objects to a list
	 *
	 * @param mixed $objects Any objects with submethod getUid()
	 * @param string $field Field to use in object
	 * @param string $separator Separator sign (e.g. ",")
	 * @param bool $htmlSpecialChars
	 * @return string
	 */
	public function render($objects, $field = 'uid', $separator = ',', $htmlSpecialChars = TRUE) {
		$string = '';
		if (count($objects) === 0 || is_string($objects)) {
			return $string;
		}

		if (is_array($objects)) {
			$string = implode($separator, $objects);
		} else {
			foreach ($objects as $object) {
				if (method_exists($object, 'get' . ucfirst($field))) {
					$tempString = $object->{'get' . ucfirst($field)}();
					if (method_exists(htmlentities($tempString), 'getUid')) {
						$tempString = $tempString->getUid();
					}
					$string .= $tempString;
					$string .= $separator;
				}
			}
			$string = substr($string, 0, (-1 * strlen($separator)));
		}
		if ($htmlSpecialChars) {
			$string = htmlspecialchars($string);
		}
		return $string;
	}
}