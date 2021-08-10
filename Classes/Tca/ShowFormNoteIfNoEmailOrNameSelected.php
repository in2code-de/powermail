<?php
declare(strict_types = 1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class ShowFormNoteIfNoEmailOrNameSelected shows one or two warnings in backend below a form if
 *      - a form has now chosen sender-name or sender-email
 *      - a form contains two fields with the same markername
 */
class ShowFormNoteIfNoEmailOrNameSelected extends AbstractFormElement
{

    /**
     * @return array
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $result['html'] = $this->getHtml();
        return $result;
    }

    /**
     * @var string
     */
    protected $templatePathAndFile =
        'EXT:powermail/Resources/Private/Templates/Tca/ShowFormNoteIfNoEmailOrNameSelected.html';

    /**
     * Path to locallang file (with : as postfix)
     *
     * @var string
     */
    protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:';

    /**
     * @return string
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    protected function getHtml(): string
    {
        if ($this->shouldNotebeShown()) {
            $standaloneView = TemplateUtility::getDefaultStandAloneView();
            $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
            $standaloneView->assignMultiple(
                [
                    'mutedNote' => $this->isNoteMuted(),
                    'form' => $this->data['databaseRow'],
                    'labels' => $this->getLabels(),
                    'markerWarning' => $this->hasFormUniqueAndFilledFieldMarkers() === false
                ]
            );
            return $standaloneView->render();
        }
        return '';
    }

    /**
     * @return bool
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function shouldNotebeShown(): bool
    {
        return $this->canBeRendered() && $this->senderEmailOrSenderNameSet() === false;
    }

    /**
     * Check if notefield was disabled
     *
     * @return bool
     */
    protected function isNoteMuted(): bool
    {
        return isset($this->data['databaseRow']['note']) && (int)$this->data['databaseRow']['note'] === 1;
    }

    /**
     * Check if showNote can be rendered:
     *      - Do we have a form uid (form is stored) AND
     *      - Is ReplaceIrre Feature disabled
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function canBeRendered(): bool
    {
        return !empty($this->data['databaseRow']['uid'])
            && MathUtility::canBeInterpretedAsInteger($this->data['databaseRow']['uid'])
            && !ConfigurationUtility::isReplaceIrreWithElementBrowserActive();
    }

    /**
     * Check if sender_email or sender_name was set
     *
     * @return bool
     * @throws Exception
     */
    protected function senderEmailOrSenderNameSet(): bool
    {
        $formIdentifier = $this->data['databaseRow']['uid'];
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $fields = $formRepository->getFieldsFromFormWithSelectQuery($formIdentifier);
        foreach ($fields as $property) {
            foreach ($property as $column => $value) {
                if ($column === 'sender_email' && (int)$value === 1) {
                    return true;
                }
                if ($column === 'sender_name' && (int)$value === 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getLabels(): array
    {
        return [
            'note1' => $this->getLabel('note.1'),
            'note2' => $this->getLabel('note.2'),
            'note3' => $this->getLabel('note.3'),
            'note4' => $this->getLabel('note.4'),
            'error1' => $this->getLabel('error.1'),
            'error2' => $this->getLabel('error.2')
        ];
    }

    /**
     * Get localized label
     *
     * @param string $key
     * @return string
     */
    protected function getLabel(string $key): string
    {
        $languageService = ObjectUtility::getLanguageService();
        return htmlspecialchars($languageService->sL($this->locallangPath . Form::TABLE_NAME . '.' . $key));
    }

    /**
     * Check if form has unique and filled field markers
     *
     * @return bool
     * @throws Exception
     */
    protected function hasFormUniqueAndFilledFieldMarkers(): bool
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $fields = $formRepository->getFieldsFromFormWithSelectQuery($this->data['databaseRow']['uid']);
        $markers = [];
        foreach ($fields as $field) {
            if (empty($field['marker'])) {
                return false;
            }
            $markers[] = $field['marker'];
        }
        return array_unique($markers) === $markers;
    }
}
