<?php
declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ShowFormNoteIfNoEmailOrNameSelected shows one or two warnings in backend below a form if
 *      - a form has now chosen sender-name or sender-email
 *      - a form contains two fields with the same markername
 */
class ShowFormNoteIfNoEmailOrNameSelected
{

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
     * @param array $params
     * @return string
     */
    public function showNote(array $params)
    {
        if ($this->isShowNote($params)) {
            $standaloneView = TemplateUtility::getDefaultStandAloneView();
            $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
            $standaloneView->assignMultiple(
                [
                    'mutedNote' => $this->isNoteMuted($params),
                    'form' => $params['row'],
                    'labels' => $this->getLabels(),
                    'markerWarning' => !$this->hasFormUniqueAndFilledFieldMarkers((int)$params['row']['uid'])
                ]
            );
            return $standaloneView->render();
        }
        return '';
    }

    /**
     * Show note
     *
     * @param array $params
     * @return bool
     */
    protected function isShowNote(array $params)
    {
        return $this->canBeRendered($params) && !$this->senderEmailOrSenderNameSet((int)$params['row']['uid']);
    }

    /**
     * Check if notefield was disabled
     *
     * @param array $params Config Array
     * @return bool
     */
    protected function isNoteMuted($params)
    {
        return isset($params['row']['note']) && (int)$params['row']['note'] === 1;
    }

    /**
     * Check if showNote can be rendered:
     *      - Do we have a form uid (form is stored) AND
     *      - Is ReplaceIrre Feature disabled
     *
     * @param array $params Config Array
     * @return bool
     */
    protected function canBeRendered(array $params)
    {
        return !empty($params['row']['uid']) && is_numeric($params['row']['uid']) &&
            !ConfigurationUtility::isReplaceIrreWithElementBrowserActive();
    }

    /**
     * Check if sender_email or sender_name was set
     *
     * @param int $formIdentifier
     * @return bool
     */
    protected function senderEmailOrSenderNameSet($formIdentifier)
    {
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
    protected function getLabels()
    {
        $labels = [
            'note1' => $this->getLabel('note.1'),
            'note2' => $this->getLabel('note.2'),
            'note3' => $this->getLabel('note.3'),
            'note4' => $this->getLabel('note.4'),
            'error1' => $this->getLabel('error.1'),
            'error2' => $this->getLabel('error.2')
        ];
        return $labels;
    }

    /**
     * Get localized label
     *
     * @param string $key
     * @return string
     */
    protected function getLabel($key)
    {
        $languageService = ObjectUtility::getLanguageService();
        return htmlspecialchars($languageService->sL($this->locallangPath . Form::TABLE_NAME . '.' . $key));
    }

    /**
     * Check if form has unique and filled field markers
     *
     * @param int $formIdentifier
     * @return bool
     */
    protected function hasFormUniqueAndFilledFieldMarkers($formIdentifier)
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $fields = $formRepository->getFieldsFromFormWithSelectQuery($formIdentifier);
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
