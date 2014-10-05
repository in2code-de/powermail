<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use \TYPO3\CMS\Core\Utility\GeneralUtility,
	\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Parses Variables for powermail
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class VariablesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Div Methods
	 *
	 * @var \In2code\Powermail\Utility\Div
	 * @inject
	 */
	protected $div;

	/**
	 * Configuration
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Parses variables again
	 *
	 * @param array $variablesMarkers Variables and Markers array
	 * @param \In2code\Powermail\Domain\Model\Mail $mail Variables and Labels array
	 * @param string $type "web" or "mail"
	 * @return string Changed string
	 */
	public function render($variablesMarkers = array(), \In2code\Powermail\Domain\Model\Mail $mail, $type = 'web') {
		$parseObject = $this->objectManager->get('\TYPO3\CMS\Fluid\View\StandaloneView');
		$parseObject->setTemplateSource($this->removePowermailAllParagraphTagWrap($this->renderChildren()));
		$parseObject->assignMultiple($this->div->htmlspecialcharsOnArray($variablesMarkers));

		$powermailAll = $this->div->powermailAll($mail, $type, $this->settings);
		$parseObject->assign('powermail_all', $powermailAll);

		return html_entity_decode($parseObject->render(), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Get renderChildren
	 * 		<p>{powermail_all}</p> =>
	 * 			{powermail_all}
	 *
	 * @param string $content
	 * @return string
	 */
	protected function removePowermailAllParagraphTagWrap($content) {
		return preg_replace(
			'#<p([^>]*)>\s*{powermail_all}\s*<\/p>#',
			'{powermail_all}',
			$content
		);
	}

	/**
	 * Init to get TypoScript Configuration
	 *
	 * @return void
	 */
	public function initialize() {
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		if (!empty($typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'])) {
			$this->settings = GeneralUtility::removeDotsFromTS(
				$typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.']
			);
		}
	}
}