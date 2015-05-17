<?php
namespace In2code\Powermail\ViewHelpers\Misc;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use In2code\Powermail\Domain\Model\Answer;

/**
 * Class ManipulateValueWithTypoScriptViewHelper for {powermail_all} variable
 *
 * @package In2code\Powermail\ViewHelpers\Misc
 */
class ManipulateValueWithTypoScriptViewHelper extends AbstractViewHelper {

	/**
	 * @var array
	 */
	protected $typeToTypoScriptType = array(
		'createAction' => 'submitPage',
		'confirmationAction' => 'confirmationPage',
		'sender' => 'senderMail',
		'receiver' => 'receiverMail',
		'optin' => 'optinMail'
	);

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObjectRenderer;

	/**
	 * TypoScript for manipulateVariablesInPowermailAllMarker
	 *
	 * @var array
	 */
	protected $typoScriptContext;

	/**
	 * Manipulate values through TypoScript before rendering
	 *
	 * @param Answer $answer
	 * @param string $type "createAction", "confirmationAction", "sender", "receiver"
	 * @return string
	 */
	public function render(Answer $answer, $type) {
		$value = $this->renderChildren();
		if ($answer->getField()) {
			if (!empty($this->typoScriptContext[$this->typeToTypoScriptType[$type] . '.'][$answer->getField()->getMarker()])) {
				$this->contentObjectRenderer->start($answer->_getProperties());
				$value = $this->contentObjectRenderer->cObjGetSingle(
					$this->typoScriptContext[$this->typeToTypoScriptType[$type] . '.'][$answer->getField()->getMarker()],
					$this->typoScriptContext[$this->typeToTypoScriptType[$type] . '.'][$answer->getField()->getMarker() . '.']
				);
			}
		}
		return $value;
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->contentObjectRenderer = $this->objectManager->get('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
		$typoScriptSetup = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
		);
		$this->typoScriptContext =
			$typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.']['manipulateVariablesInPowermailAllMarker.'];
	}
}