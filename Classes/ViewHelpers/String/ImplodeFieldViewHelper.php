<?php
namespace In2code\Powermail\ViewHelpers\String;

/**
 * View helper to implode an array or objects to a list
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class ImplodeFieldViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * View helper to implode an array or objects to a list
	 *
	 * @param mixed $objects Any objects with submethod getUid()
	 * @param string $field Field to use in object
	 * @param string $separator Separator sign (e.g. ",")
	 * @return string
	 */
	public function render($objects, $field = 'uid', $separator = ',') {
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
					if (method_exists($tempString, 'getUid')) {
						$tempString = $tempString->getUid();
					}
					$string .= $tempString;
					$string .= $separator;
				}
			}
			$string = substr($string, 0, (-1 * strlen($separator)));
		}
		return $string;
	}
}