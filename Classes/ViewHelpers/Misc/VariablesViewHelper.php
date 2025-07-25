<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class VariablesViewHelper
 */
class VariablesViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Configuration
     */
    protected array $settings = [];

    /**
     * @var RenderingContext
     */
    protected $renderingContext;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('mail', Mail::class, 'Mail', true);
        $this->registerArgument('type', 'string', '"web" or "mail"', false, 'web');
        $this->registerArgument('function', 'string', 'createAction, senderMail, receiverMail', false, 'createAction');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $mail = $this->arguments['mail'];
        $type = $this->arguments['type'];
        $function = $this->arguments['function'];
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $parseObject = GeneralUtility::makeInstance(StandaloneView::class);
        $parseObject->setRequest($this->renderingContext->getRequest());
        $parseObject->setTemplateSource($this->removePowermailAllParagraphTagWrap($this->renderChildren()));
        $parseObject->assignMultiple(
            ArrayUtility::htmlspecialcharsOnArray($mailRepository->getVariablesWithMarkersFromMail($mail))
        );
        $parseObject->assignMultiple(
            ArrayUtility::htmlspecialcharsOnArray($mailRepository->getLabelsWithMarkersFromMail($mail))
        );
        $parseObject->assign('powermail_all', TemplateUtility::powermailAll($mail, $type, $this->settings, $function));
        return html_entity_decode($parseObject->render(), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Helper method which triggers the rendering of everything between the
     * opening and the closing tag. In addition change -&gt; to ->
     *
     * @return mixed The finally rendered child nodes.
     */
    public function renderChildren()
    {
        $content = parent::renderChildren();
        return str_replace('-&gt;', '->', $content);
    }

    /**
     * Get renderChildren
     *        <p>{powermail_all}</p> =>
     *            {powermail_all}
     */
    protected function removePowermailAllParagraphTagWrap(string $content): string
    {
        return preg_replace('#<p([^>]*)>\s*{powermail_all}\s*<\/p>#', '{powermail_all}', $content);
    }

    /**
     * Init to get TypoScript Configuration
     */
    public function initialize(): void
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->settings = $configurationService->getTypoScriptSettings();
    }
}
