<?php
namespace In2code\Powermail\Utility\Tca;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ShowFormNoteIfNoEmailOrNameSelected shows note if form errors
 */
class ShowFormNoteIfNoEmailOrNameSelected
{

    /**
     * Path to locallang file (with : as postfix)
     *
     * @var string
     */
    protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:';

    /**
     * @var \TYPO3\CMS\Lang\LanguageService
     */
    protected $languageService = null;

    /**
     * Show Note if no Email or Name selected
     *
     * @param array $params Config Array
     * @return string
     */
    public function showNote($params)
    {
        $this->initialize();
        $content = '';
        if (!$this->showNoteActive($params)) {
            return $content;
        }

        if (!$this->senderEmailOrSenderNameSet($params['row']['uid'])) {
            if ($this->noteFieldDisabled($params)) {
                $content .= '<p style="opacity: 0.3; margin: 0;">';
                $content .= $this->getCheckboxHtml($params);
                $content .= '<label for="' . Form::TABLE_NAME . '_note_checkbox" style="vertical-align: bottom;">';
                $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.note.4', true);
                $content .= '</label>';
                $content .= '<p style="margin: 0 0 3px 0;">';
            } else {
                $content .= '<div style="background-color: #FCF8E3; border: 1px solid #FFB019;' .
                    ' padding: 5px 10px; color: #FFB019;">';
                $content .= '<p style="margin: 0 0 3px 0;">';
                $content .= '<strong>';
                $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.note.1', true);
                $content .= '</strong>';
                $content .= ' ';
                $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.note.2', true);
                $content .= '</p>';
                $content .= '<p style="margin: 0;">';
                $content .= $this->getCheckboxHtml($params);
                $content .= '<label for="' . Form::TABLE_NAME . '_note_checkbox" style="vertical-align: bottom;">';
                $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.note.3', true);
                $content .= '</label>';
                $content .= '</p>';
                $content .= '</div>';
            }
        }

        if (!$this->hasFormUniqueAndFilledFieldMarkers($params['row']['uid'])) {
            $content .= '<div style="background:#F2DEDE; border:1px solid #A94442;' .
                ' padding: 5px 10px; color: #A94442; margin-top: 10px">';
            $content .= '<p><strong>';
            $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.error.1', true);
            $content .= '</strong></p>';
            $content .= '<p>';
            $content .= $this->languageService->sL($this->locallangPath . Form::TABLE_NAME . '.error.2', true);
            $content .= '</p>';
            $content .= '</div>';
        }

        return $content;
    }

    /**
     * @param array $params Config Array
     * @return string
     */
    protected function getCheckboxHtml($params)
    {
        $content = '';
        $content .= '<input type="checkbox" id="' . Form::TABLE_NAME . '_note_checkbox" name="dummy" ';
        $content .= ((isset($params['row']['note']) && $params['row']['note'] === '1') ? 'checked="checked" ' : '');
        $content .= 'value="1" onclick="document.getElementById' .
            '(\'' . Form::TABLE_NAME . '_note\').value = ((this.checked) ? 1 : 0);" />';
        $content .= '<input type="hidden" id="' . Form::TABLE_NAME . '_note" ';
        $content .= 'name="data[' . Form::TABLE_NAME . '][' . $params['row']['uid'] . '][note]" ';
        $content .= 'value="' . (!empty($params['row']['note']) ? '1' : '0') . '" />';
        return $content;
    }

    /**
     * Check if notefield was disabled
     *
     * @param array $params Config Array
     * @return bool
     */
    protected function noteFieldDisabled($params)
    {
        if (isset($params['row']['note']) && $params['row']['note'] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Check if sender_email or sender_name was set
     *
     * @param $formUid
     * @return bool
     */
    protected function senderEmailOrSenderNameSet($formUid)
    {
        /** @var FormRepository $formRepository */
        $formRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(FormRepository::class);
        $fields = $formRepository->getFieldsFromFormWithSelectQuery($formUid);
        foreach ($fields as $property) {
            foreach ($property as $column => $value) {
                if ($column === 'sender_email' && $value === '1') {
                    return true;
                }
                if ($column === 'sender_name' && $value === '1') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if form has unique and filled field markers
     *
     * @param $formUid
     * @return bool
     */
    protected function hasFormUniqueAndFilledFieldMarkers($formUid)
    {
        /** @var FormRepository $formRepository */
        $formRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(FormRepository::class);
        $fields = $formRepository->getFieldsFromFormWithSelectQuery($formUid);
        $markers = [];
        foreach ($fields as $field) {
            if (empty($field['marker'])) {
                return false;
            }
            $markers[] = $field['marker'];
        }
        if (array_unique($markers) !== $markers) {
            return false;
        }
        return true;
    }

    /**
     * Check if showNote should be active or not
     *
     * @param array $params Config Array
     * @return bool
     */
    protected function showNoteActive($params)
    {
        if (
            !isset($params['row']['uid']) ||
            !is_numeric($params['row']['uid']) ||
            ConfigurationUtility::isReplaceIrreWithElementBrowserActive()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Initialize some variables
     *
     * @return void
     */
    protected function initialize()
    {
        $this->languageService = $GLOBALS['LANG'];
    }
}
