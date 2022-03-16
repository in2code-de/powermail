<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CheckForTypoScriptViewHelper
 * @noinspection PhpUnused
 */
class CheckForTypoScriptViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('settings', 'array', 'settings array', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): void {
        $argumentsSettings = $arguments['settings'] ?? [];
        if (($argumentsSettings['staticTemplate'] ?? 1) !== '1') {
            /** @var FlashMessageQueue $flashMessageQueue */
            $flashMessageQueue = $renderingContext->getControllerContext()->getFlashMessageQueue(null);
            /** @var FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                LocalizationUtility::translate('error_no_typoscript'),
                '',
                AbstractMessage::ERROR
            );
            $flashMessageQueue->addMessage($flashMessage);
        }
    }
}
