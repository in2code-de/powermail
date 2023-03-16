<?php

declare(strict_types=1);

namespace In2code\Powermail\Hook;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
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

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $row = $item->getRecord();

        $flexforms = GeneralUtility::xml2array($row['pi_flexform']);
        if (is_string($flexforms)) {
            return 'ERROR: ' . htmlspecialchars($flexforms);
        }
        $this->flexFormData = (array)$flexforms;

        $preview = '';
        if (!ConfigurationUtility::isDisablePluginInformationActive() && !empty($this->flexFormData)) {
            switch ($row['CType']) {
                case 'powermail_pi1':
                    $preview = $this->getPluginInformation('Pi1', $row);
                    break;
                case 'powermail_pi2':
                    $preview = $this->getPluginInformation('Pi2', $row);
                    break;
                case 'powermail_pi3':
                    $preview = $this->getPluginInformation('Pi3', $row);
                    break;
                case 'powermail_pi4':
                    $preview = $this->getPluginInformation('Pi4', $row);
                    break;
                default:
                    $preview = '';
            }
        }
        return $preview;
    }

    /**
     * @param string @pluginName
     * @return string
     * @throws InvalidConfigurationTypeException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
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
                    (int)$this->getFieldFromFlexform('settings.flexform.main.form', 'main'),
                    $row['sys_language_uid']
                ),
                'receiverEmail' => $this->getReceiverEmail(),
                'receiverEmailDevelopmentContext' => ConfigurationUtility::getDevelopmentContextEmail(),
                'mails' => $this->getLatestMails(),
                'pluginName' => $pluginName,
                'enableMailPreview' => !ConfigurationUtility::isDisablePluginInformationMailPreviewActive(),
                'form' => $this->getFormTitleByUid(
                    (int)$this->getFieldFromFlexform('settings.flexform.main.form', 'main')
                ),
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Get latest three emails to this form
     *
     * @return QueryResultInterface
     */
    protected function getLatestMails(): QueryResultInterface
    {
        /** @var MailRepository $mailRepository */
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        return $mailRepository->findLatestByForm(
            (int)$this->getFieldFromFlexform('settings.flexform.main.form', 'main')
        );
    }

    /**
     * Get receiver mail
     *
     * @return string
     */
    protected function getReceiverEmail(): string
    {
        $receiver = $this->getFieldFromFlexform('settings.flexform.receiver.email', 'receiver');
        if ((int)$this->getFieldFromFlexform('settings.flexform.receiver.type', 'receiver') === 1) {
            $receiver = 'Frontenduser Group '
                . (int)$this->getFieldFromFlexform('settings.flexform.receiver.fe_group', 'receiver');
        }
        if ((int)$this->getFieldFromFlexform('settings.flexform.receiver.type', 'receiver') === 2) {
            $receiver = 'Predefined "' .
                (int)$this->getFieldFromFlexform('settings.flexform.receiver.predefinedemail', 'receiver') . '"';
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
                (int)$uid,
                (int)$sysLanguageUid
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

    public function getFieldFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        $flexform = $this->flexFormData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }
        return null;
    }
}
