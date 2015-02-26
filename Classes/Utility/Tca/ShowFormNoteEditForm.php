<?php
namespace In2code\Powermail\Utility\Tca;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Utility\Div;

/**
 * Class ShowFormNoteEditForm shows note in FlexForm
 */
class ShowFormNoteEditForm {

	/**
	 * Path to locallang file (with : as postfix)
	 *
	 * @var string
	 */
	protected $locallangPath = 'LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:';

	/**
	 * Show Note which form was selected
	 *
	 * @param array $pa Config Array
	 * @param object $fobj Parent Object
	 * @return string
	 */
	public function showNote($pa, $fobj) {
		$content = '';
		$formUid = $this->getRelatedForm($pa);

		if ($formUid) {
			$returnUrl = rawurlencode(
				Div::getSubFolderOfCurrentUrl() . GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT')
			);
			$editFormLink = Div::getSubFolderOfCurrentUrl();
			$editFormLink .= 'typo3/alt_doc.php?edit[tx_powermail_domain_model_forms][' . $formUid . ']=edit';
			$editFormLink .= '&returnUrl=' . $returnUrl;

			$content .= '
				<table cellspacing="0" cellpadding="0" border="0" class="typo3-dblist" style="border: 1px solid #d7d7d7;">
					<tbody>
						<tr class="t3-row-header">
							<td nowrap="nowrap" style="padding: 5px; color: white">
								<span class="c-table">
									' . $this->getLabel('flexform.main.formnote.formname') . '
								</span>
							</td>
							<td nowrap="nowrap" style="padding: 5px; color: white">
								<span class="c-table">
									' . $this->getLabel('flexform.main.formnote.storedinpage') . '
								</span>
							</td>
							<td nowrap="nowrap" style="padding: 5px; color: white">
								<span class="c-table">
									' . $this->getLabel('flexform.main.formnote.pages') . '
								</span>
							</td>
							<td nowrap="nowrap" style="padding: 5px; color: white">
								<span class="c-table">
									' . $this->getLabel('flexform.main.formnote.fields') . '
								</span>
							</td>
							<td nowrap="nowrap" style="padding: 5px; color: white">
								<span class="c-table">
									&nbsp;
								</span>
							</td>
						</tr>
						<tr class="db_list_normal">
							<td nowrap="nowrap" class="col-title" style="padding: 5px;">
								<a title="Edit" href="' . $editFormLink . '">
									' . htmlspecialchars($this->getFormPropertyFromUid($formUid, 'title')) . '
								</a>
							</td>
							<td nowrap="nowrap" class="col-title" style="padding: 5px;">
								<a title="id=' . $this->getFormPropertyFromUid($formUid, 'pid') . '"
									onclick="top.loadEditId(' . intval($this->getFormPropertyFromUid($formUid, 'pid')) . '
									,&quot;&amp;SET[language]=0&quot;); return false;" href="#">
									' . htmlspecialchars($this->getPageNameFromUid($this->getFormPropertyFromUid($formUid, 'pid'))) . '
								</a>
							</td>
							<td nowrap="nowrap" class="col-title" style="padding: 5px;">
								<span title="' . htmlspecialchars(implode(', ', $this->getPagesFromForm($formUid))) . '">
									' . count($this->getPagesFromForm($formUid)) . '
								</span>
							</td>
							<td nowrap="nowrap" class="col-title" style="padding: 5px;">
								<span title="' . htmlspecialchars(implode(', ', $this->getFieldsFromForm($formUid))) . '">
									' . count($this->getFieldsFromForm($formUid)) . '
								</span>
							</td>
							<td nowrap="nowrap" class="col-icon" style="padding: 5px;">
								<a title="Edit" href="' . $editFormLink . '">
									<span class="t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-open"
										title="Edit Form">
										&nbsp;
									</span>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			';
		} else {
			$content .= '<div style="padding-bottom: 5px;">';
			$content .= $this->getLabel('flexform.main.formnote.noform');
			$content .= '</div>';
		}

		return $content;
	}

	/**
	 * Get localized label
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getLabel($key) {
		return $GLOBALS['LANG']->sL($this->locallangPath . $key, TRUE);
	}

	/**
	 * Get related form
	 *
	 * @param array $pa Config Array
	 * @return int
	 */
	protected function getRelatedForm($pa) {
		$flexForm = GeneralUtility::xml2array($pa['row']['pi_flexform']);
		if (is_array($flexForm) && isset($flexForm['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
			return intval($flexForm['data']['main']['lDEF']['settings.flexform.main.form']['vDEF']);
		}
		return 0;
	}

	/**
	 * @param int $uid
	 * @param string $property
	 * @return string
	 */
	protected function getFormPropertyFromUid($uid, $property) {
		$select = '*';
		$from = 'tx_powermail_domain_model_forms';
		$where = 'uid = ' . intval($uid);
		$groupBy = '';
		$orderBy = '';
		$limit = 1;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (isset($row[$property])) {
			return $row[$property];
		}
		return '';
	}

	/**
	 * @param int $uid
	 * @return string
	 */
	protected function getPageNameFromUid($uid) {
		$select = 'title';
		$from = 'pages';
		$where = 'uid = ' . intval($uid);
		$groupBy = '';
		$orderBy = '';
		$limit = 1;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (isset($row['title'])) {
			return $row['title'];
		}
		return '';
	}

	/**
	 * Get array with related pages to a form
	 *
	 * @param int $uid
	 * @return array
	 */
	protected function getPagesFromForm($uid) {
		$result = array();
		$select = 'tx_powermail_domain_model_pages.title';
		$from = '
			tx_powermail_domain_model_forms
			LEFT JOIN tx_powermail_domain_model_pages ON tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
		';
		$where = 'tx_powermail_domain_model_forms.uid = ' . intval($uid) .
			' and tx_powermail_domain_model_pages.deleted = 0';
		$groupBy = '';
		$orderBy = '';
		$limit = 1000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$result[] = $row['title'];
			}
		}
		return $result;
	}

	/**
	 * Get array with related fields to a form
	 *
	 * @param int $uid
	 * @return array
	 */
	protected function getFieldsFromForm($uid) {
		$result = array();
		$select = 'tx_powermail_domain_model_fields.title';
		$from = '
			tx_powermail_domain_model_forms
			LEFT JOIN tx_powermail_domain_model_pages ON tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
			LEFT JOIN tx_powermail_domain_model_fields ON tx_powermail_domain_model_fields.pages = tx_powermail_domain_model_pages.uid
		';
		$where = 'tx_powermail_domain_model_forms.uid = ' . intval($uid) .
			' and tx_powermail_domain_model_pages.deleted = 0
			 and tx_powermail_domain_model_fields.deleted = 0';
		$groupBy = '';
		$orderBy = '';
		$limit = 1000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$result[] = $row['title'];
			}
		}
		return $result;
	}
}