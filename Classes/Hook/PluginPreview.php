<?php
declare(strict_types = 1);
namespace In2code\Powermail\Hook;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Contains a preview rendering for the powermail page module
 */
class PluginPreview implements PageLayoutViewDrawItemHookInterface
{

    /**
     * @var array
     */
    protected $row = [];

    /**
     * @var array
     */
    protected $flexFormData;

    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:powermail/Resources/Private/Templates/Hook/PluginPreview.html';

    /**
     * Preprocesses the preview rendering of a content element
     *
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        if (!ConfigurationUtility::isDisablePluginInformationActive()) {
            if ($row['CType'] === 'list') {
                switch ($row['list_type']) {
                    case 'powermail_pi1':
                        $this->initialize($row);
                        $drawItem = false;
                        $headerContent = '';
                        $itemContent = $this->getPluginInformation('Pi1');
                        break;
                    case 'powermail_pi2':
                        $this->initialize($row);
                        $drawItem = false;
                        $headerContent = '';
                        $itemContent = $this->getPluginInformation('Pi2');
                        break;
                    default:
                }
            }
        }
    }

    /**
     * @param string @pluginName
     * @return string
     * @throws Exception
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function getPluginInformation(string $pluginName): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple(
            [
                'row' => $this->row,
                'flexFormData' => $this->flexFormData,
                'formUid' => $this->getLocalizedFormUid($this->getFormUid(), $this->getSysLanguageUid()),
                'receiverEmail' => $this->getReceiverEmail(),
                'receiverEmailDevelopmentContext' => ConfigurationUtility::getDevelopmentContextEmail(),
                'mails' => $this->getLatestMails(),
                'pluginName' => $pluginName,
                'enableMailPreview' => !ConfigurationUtility::isDisablePluginInformationMailPreviewActive(),
                'form' => $this->getFormTitleByUid(
                    (int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.main.form')
                )
            ]
        );
        return $standaloneView->render();
    }

    /**
     * Get latest three emails to this form
     *
     * @return QueryResultInterface
     * @throws Exception
     */
    protected function getLatestMails(): QueryResultInterface
    {
        /** @var MailRepository $mailRepository */
        $mailRepository = ObjectUtility::getObjectManager()->get(MailRepository::class);
        return $mailRepository->findLatestByForm(
            (int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.main.form')
        );
    }

    /**
     * Get receiver mail
     *
     * @return string
     */
    protected function getReceiverEmail(): string
    {
        $receiver = ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.email');
        if ((int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.type') === 1) {
            $receiver = 'Frontenduser Group '
                . ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.fe_group');
        }
        if ((int)ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.type') === 2) {
            $receiver = 'Predefined "' .
                ArrayUtility::getValueByPath($this->flexFormData, 'settings.flexform.receiver.predefinedemail') . '"';
        }
        return $receiver;
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
        return (string)$row['title'];
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
     * Get form uid
     *
     * @return int
     */
    protected function getFormUid(): int
    {
        return (int)$this->flexFormData['settings']['flexform']['main']['form'];
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

    /**
     * @param array $row
     * @return void
     * @throws Exception
     */
    protected function initialize(array $row): void
    {
        $this->row = $row;
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        $this->flexFormData = $flexFormService->convertFlexFormContentToArray($this->row['pi_flexform']);
    }
}
