<?php

declare(strict_types=1);

namespace In2code\Powermail\Hook;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Events\BackendPageModulePreviewContentEvent;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TemplateUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Contains a preview rendering for the powermail page module
 */
class PluginPreviewRenderer extends StandardContentPreviewRenderer
{
    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @var array
     */
    protected array $flexFormData = [];

    /**
     * @var string
     */
    protected string $templatePathAndFile = 'EXT:powermail/Resources/Private/Templates/Hook/PluginPreview.html';

    /**
     * @throws InvalidConfigurationTypeException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $row = $item->getRecord();

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

        $flexforms = $flexFormService->convertFlexFormContentToArray($row['pi_flexform']);

        if (!is_array($flexforms)) {
            return 'ERROR: ' . htmlspecialchars($flexforms);
        }
        $this->flexFormData = $flexforms;

        $preview = '';
        if (!ConfigurationUtility::isDisablePluginInformationActive() && !empty($this->flexFormData)) {
            $preview = match ($row['CType']) {
                'powermail_pi1' => $this->getPluginInformation('Pi1', $row),
                'powermail_pi2' => $this->getPluginInformation('Pi2', $row),
                'powermail_pi3' => $this->getPluginInformation('Pi3', $row),
                'powermail_pi4' => $this->getPluginInformation('Pi4', $row),
                default => '',
            };
        }

        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $event = $eventDispatcher->dispatch(
            new BackendPageModulePreviewContentEvent($preview, $item)
        );
        return $event->getPreview();
    }

    /**
     * @param string $pluginName @pluginName
     * @param array $row
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     */
    protected function getPluginInformation(string $pluginName, array $row): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple(
            [
                'row' => $row,
                'flexFormData' => $this->flexFormData,
                'formUid' => $this->getLocalizedFormUid(
                    (int)$this->flexFormData['settings']['flexform']['main']['form'],
                    $row['sys_language_uid']
                ),
                'receiverEmail' => $this->getReceiverEmail(),
                'receiverEmailDevelopmentContext' => ConfigurationUtility::getDevelopmentContextEmail(),
                'mails' => $this->getLatestMails($row),
                'pluginName' => $pluginName,
                'enableMailPreview' => !ConfigurationUtility::isDisablePluginInformationMailPreviewActive(),
                'form' => $this->getFormTitleByUid(
                    (int)$this->flexFormData['settings']['flexform']['main']['form'],
                ),
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Get latest three emails to this form
     *
     * @param $row
     * @return QueryResultInterface
     */
    protected function getLatestMails($row): QueryResultInterface
    {
        /** @var MailRepository $mailRepository */
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        return $mailRepository->findLatestByFormAndPage(
            (int)$this->flexFormData['settings']['flexform']['main']['form'],
            (int)$row['pid']
        );
    }

    /**
     * Get receiver mail
     *
     * @return string
     */
    protected function getReceiverEmail(): string
    {
        $receiver = $this->flexFormData['settings']['flexform']['receiver']['email'] ?? '';
        if (
            isset($this->flexFormData['settings']['flexform']['receiver']['type']) &&
            isset($this->flexFormData['settings']['flexform']['receiver']['fe_group']) &&
            (int)$this->flexFormData['settings']['flexform']['receiver']['type'] === 1
        ) {
            $receiver = 'Frontenduser Group '
                . (int)$this->flexFormData['settings']['flexform']['receiver']['fe_group'] ?? 0;
        }
        if (
            isset($this->flexFormData['settings']['flexform']['receiver']['type']) &&
            isset($this->flexFormData['settings']['flexform']['receiver']['predefinedemail']) &&
            (int)$this->flexFormData['settings']['flexform']['receiver']['type'] === 2) {
            $receiver = 'Predefined "'
                . (int)$this->flexFormData['settings']['flexform']['receiver']['predefinedemail'] . '"';
        }
        return $receiver ?? '';
    }

    /**
     * Get form title from uid
     *
     * @param int $uid Form uid
     * @return string
     */
    protected function getFormTitleByUid(int $uid): string
    {
        $uid = $this->getLocalizedFormUid($uid, $this->getSysLanguageUid());
        $row = BackendUtilityCore::getRecord(Form::TABLE_NAME, $uid, 'title', '', false);
        return $row['title'] ?? '';
    }

    /**
     * Get form uid of a localized form
     *
     * @param int $uid
     * @param int $sysLanguageUid
     * @return int
     */
    protected function getLocalizedFormUid(int $uid, int $sysLanguageUid): int
    {
        if ($sysLanguageUid > 0) {
            $row = BackendUtilityCore::getRecordLocalization(
                Form::TABLE_NAME,
                $uid,
                $sysLanguageUid
            );
            if ($row && !empty($row[0]['uid'])) {
                $uid = (int)$row[0]['uid'];
            }
        }
        return $uid;
    }

    /**
     * Get current sys_language_uid from page content
     *
     * @return int
     */
    protected function getSysLanguageUid(): int
    {
        if (!empty($this->row['sys_language_uid'])) {
            return (int)$this->row['sys_language_uid'];
        }
        return 0;
    }
}
