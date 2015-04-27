<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Link for Powermail Assets on Backend Call
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class LinkViewHelper extends AbstractViewHelper {

	/**
	 * Link for Powermail Assets on Backend Call
	 *
	 * @param string $path like uploads/tx_powermail/file.txt
	 * @param bool $absolute
	 * @return string
	 */
	public function render($path, $absolute = FALSE) {
		$uri = '';
		if ($absolute) {
			$uri .= parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'), PHP_URL_SCHEME);
			$uri .= '://' . GeneralUtility::getIndpEnv('HTTP_HOST') . '/';
			$uri .= rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_PATH'), '/');
		}
		return $uri . $path;
	}
}