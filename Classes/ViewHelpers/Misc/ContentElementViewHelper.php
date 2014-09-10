<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Shows Content Element
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class ContentElementViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * Parse a content element
	 *
	 * @param \int $uid UID of any content element
	 * @return \string Parsed Content Element
	 */
	public function render($uid) {
		$conf = array(
			'tables' => 'tt_content',
			'source' => intval($uid),
			'dontCheckPid' => 1
		);
		return $this->contentObject->RECORDS($conf);
	}

	/**
	 * Injects the content object
	 *
	 * @param ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectContentObject(ConfigurationManagerInterface $configurationManager) {
		$this->contentObject = $configurationManager->getContentObject();
	}

}