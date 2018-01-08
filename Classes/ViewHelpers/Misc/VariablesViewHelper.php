<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\ConfigurationService;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Parses Variables for powermail
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
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Enable variables within variable {powermail_rte} - so string will be parsed again
     *
     * @param Mail $mail Variables and Labels array
     * @param string $type "web" or "mail"
     * @param string $function "createAction", "senderMail", "receiverMail"
     * @return string Changed string
     */
    public function render(Mail $mail, $type = 'web', $function = 'createAction')
    {
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        $parseObject = ObjectUtility::getObjectManager()->get(StandaloneView::class);
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
        $content = str_replace('-&gt;', '->', $content);
        return $content;
    }

    /**
     * Get renderChildren
     *        <p>{powermail_all}</p> =>
     *            {powermail_all}
     *
     * @param string $content
     * @return string
     */
    protected function removePowermailAllParagraphTagWrap($content)
    {
        return preg_replace('#<p([^>]*)>\s*{powermail_all}\s*<\/p>#', '{powermail_all}', $content);
    }

    /**
     * Init to get TypoScript Configuration
     *
     * @return void
     */
    public function initialize()
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $this->settings = $configurationService->getTypoScriptSettings();
    }
}
