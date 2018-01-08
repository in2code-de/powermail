<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class ManipulateValueWithTypoScriptViewHelper for {powermail_all} variable
 */
class ManipulateValueWithTypoScriptViewHelper extends AbstractViewHelper
{

    /**
     * @var array
     */
    protected $typeToTsType = [
        'createAction' => 'submitPage',
        'confirmationAction' => 'confirmationPage',
        'sender' => 'senderMail',
        'receiver' => 'receiverMail',
        'optin' => 'optinMail'
    ];

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
    public function render(Answer $answer, $type)
    {
        $value = $this->renderChildren();
        if ($answer->getField()) {
            if (!empty($this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker()])) {
                $this->contentObjectRenderer->start($answer->_getProperties());
                $value = $this->contentObjectRenderer->cObjGetSingle(
                    $this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker()],
                    $this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker() . '.']
                );
            }
        }
        return $value;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->contentObjectRenderer = $this->objectManager->get(ContentObjectRenderer::class);
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $configuration = $configurationService->getTypoScriptConfiguration();
        $this->typoScriptContext = $configuration['manipulateVariablesInPowermailAllMarker.'];
    }
}
