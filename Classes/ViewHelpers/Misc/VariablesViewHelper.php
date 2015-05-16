<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use In2code\Powermail\Domain\Model\Mail;

/**
 * Parses Variables for powermail
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class VariablesViewHelper extends AbstractViewHelper {

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
	 * @param \In2code\Powermail\Domain\Model\Mail $mail Variables and Labels array
	 * @param string $type "web" or "mail"
	 * @param string $function "createAction", "senderMail", "receiverMail"
	 * @param array $variablesMarkers - deprecated parameter - will be removed
	 * @todo remove param $variablesMarkers
	 * @return string Changed string
	 */
	public function render(Mail $mail, $type = 'web', $function = 'createAction', $variablesMarkers = array()) {
		// TODO remove $variablesMarkers completely in next minor version
		if (count($variablesMarkers)) {
			GeneralUtility::deprecationLog(
				'Method \In2code\Powermail\ViewHelpers\Misc\VariablesViewHelper::render() was called from a ' .
				'template or a partial with attribute "variablesMarkers". This attribute will be removed in next ' .
				'minor version of powermail. Further use can lead to exceptions. Please remove this attribute ' .
				'from your template files.'
			);
		}

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $parseObject */
		$parseObject = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		$parseObject->setTemplateSource($this->removePowermailAllParagraphTagWrap($this->renderChildren()));
		$parseObject->assignMultiple($this->div->htmlspecialcharsOnArray($this->div->getVariablesWithMarkersFromMail($mail)));
		$parseObject->assignMultiple($this->div->htmlspecialcharsOnArray($this->div->getLabelsWithMarkersFromMail($mail)));

		$powermailAll = $this->div->powermailAll($mail, $type, $this->settings, $function);
		$parseObject->assign('powermail_all', $powermailAll);

		return html_entity_decode($parseObject->render(), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Helper method which triggers the rendering of everything between the
	 * opening and the closing tag. In addition change -&gt; to ->
	 *
	 * @return mixed The finally rendered child nodes.
	 */
	public function renderChildren() {
		$content = parent::renderChildren();
		$content = str_replace('-&gt;', '->', $content);
		return $content;
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