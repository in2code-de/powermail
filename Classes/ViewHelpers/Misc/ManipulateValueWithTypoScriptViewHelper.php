<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ManipulateValueWithTypoScriptViewHelper for {powermail_all} variable
 */
class ManipulateValueWithTypoScriptViewHelper extends AbstractViewHelper
{
    /**
     * @var array
     */
    protected array $typeToTsType = [
        'createAction' => 'submitPage',
        'confirmationAction' => 'confirmationPage',
        'sender' => 'senderMail',
        'receiver' => 'receiverMail',
        'optin' => 'optinMail',
    ];

    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $contentObjectRenderer;

    /**
     * TypoScript for manipulateVariablesInPowermailAllMarker
     *
     * @var array
     */
    protected array $typoScriptContext;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('answer', Answer::class, 'Answer', true);
        $this->registerArgument('type', 'string', '"createAction", "confirmationAction", "sender", "receiver"', true);
    }

    /**
     * Manipulate values through TypoScript before rendering
     *
     * @return string
     */
    public function render(): string
    {
        $answer = $this->arguments['answer'];
        $type = $this->arguments['type'];
        $value = $this->renderChildren();
        if ($answer->getField()) {
            if (
                isset($this->typeToTsType[$type]) &&
                !empty($this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker()])) {
                $this->contentObjectRenderer->start($answer->_getProperties());
                $value = $this->contentObjectRenderer->cObjGetSingle(
                    $this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker()],
                    $this->typoScriptContext[$this->typeToTsType[$type] . '.'][$answer->getField()->getMarker() . '.']
                );
            }
        }
        return (string)$value;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $configuration = $configurationService->getTypoScriptConfiguration();
        $this->typoScriptContext = $configuration['manipulateVariablesInPowermailAllMarker.'];
    }
}
