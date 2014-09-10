<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Link ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class LinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 * @inject
	 */
	protected $uriBuilder;

	/**
	 * Parse a content element
	 *
	 * @param string $path like uploads/tx_powermail/
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
		$uri .= $this->uriBuilder->setTargetPageUid($path)->buildFrontendUri();
		return $uri;
	}
}