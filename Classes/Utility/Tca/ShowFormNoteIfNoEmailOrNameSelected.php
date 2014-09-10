<?php
namespace In2code\Powermail\Utility\Tca;

use \In2code\Powermail\Utility\Div;

/**
 * Class ShowFormNoteIfNoEmailOrNameSelected shows note if form errors
 */
class ShowFormNoteIfNoEmailOrNameSelected {

	/**
	 * Path to locallang file (with : as postfix)
	 *
	 * @var string
	 */
	protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:';

	/**
	 * Show Note if no Email or Name selected
	 *
	 * @param array $pa Config Array
	 * @param object $fobj Parent Object
	 * @return string
	 */
	public function showNote($pa, $fobj) {
		$content = '';
		if (!isset($pa['row']['uid']) || !is_numeric($pa['row']['uid'])) {
			return $content;
		}

		if (!$this->senderEmailOrSenderNameSet($pa['row']['uid'])) {
			if ($this->noteFieldDisabled($pa)) {
				$content .= '<p style="opacity: 0.3; margin: 0;">';
				$content .= $this->getCheckboxHtml($pa);
				$content .= '<label for="tx_powermail_domain_model_forms_note_checkbox" style="vertical-align: bottom;">';
				$content .= $GLOBALS['LANG']->sL($this->locallangPath . 'tx_powermail_domain_model_forms.note.4', TRUE);
				$content .= '</label>';
				$content .= '<p style="margin: 0 0 3px 0;">';
			} else {
				$content .= '<div style="background-color: #FCF8E3; border: 1px solid #FFB019; padding: 5px 10px; color: #FFB019;">';
				$content .= '<p style="margin: 0 0 3px 0;">';
				$content .= '<strong>';
				$content .= $GLOBALS['LANG']->sL($this->locallangPath . 'tx_powermail_domain_model_forms.note.1', TRUE);
				$content .= '</strong>';
				$content .= ' ';
				$content .= $GLOBALS['LANG']->sL($this->locallangPath . 'tx_powermail_domain_model_forms.note.2', TRUE);
				$content .= '</p>';
				$content .= '<p style="margin: 0;">';
				$content .= $this->getCheckboxHtml($pa);
				$content .= '<label for="tx_powermail_domain_model_forms_note_checkbox" style="vertical-align: bottom;">';
				$content .= $GLOBALS['LANG']->sL($this->locallangPath . 'tx_powermail_domain_model_forms.note.3', TRUE);
				$content .= '</label>';
				$content .= '</p>';
				$content .= '</div>';
			}
		}

		return $content;
	}

	/**
	 * @param array $pa Config Array
	 * @return string
	 */
	protected function getCheckboxHtml($pa) {
		$content = '';
		$content .= '<input type="checkbox" id="tx_powermail_domain_model_forms_note_checkbox" name="dummy" ';
		$content .= ((isset($pa['row']['note']) && $pa['row']['note'] === '1') ? 'checked="checked" ' : '');
		$content .= '
			value="1" onclick="document.getElementById(\'tx_powermail_domain_model_forms_note\').value = ((this.checked) ? 1 : 0);" />
		';
		$content .= '<input type="hidden" id="tx_powermail_domain_model_forms_note" ';
		$content .= 'name="data[tx_powermail_domain_model_forms][' . $pa['row']['uid'] . '][note]" ';
		$content .= 'value="' . (!empty($pa['row']['note']) ? '1' : '0') . '" />';
		return $content;
	}

	/**
	 * Check if notefield was disabled
	 *
	 * @param array $pa Config Array
	 * @return bool
	 */
	protected function noteFieldDisabled($pa) {
		if (isset($pa['row']['note']) && $pa['row']['note'] === '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if sender_email or sender_name was set
	 *
	 * @param $formUid
	 * @return bool
	 */
	protected function senderEmailOrSenderNameSet($formUid) {
		$fields = Div::getFieldsFromFormWithSelectQuery($formUid);
		foreach ($fields as $property) {
			foreach ($property as $column => $value) {
				if ($column === 'sender_email' && $value === '1') {
					return TRUE;
				}
				if ($column === 'sender_name' && $value === '1') {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}