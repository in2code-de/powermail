<?php
namespace In2code\Powermail\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Domain\Model\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * FormRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FormRepository extends Repository {

	/**
	 * Find Form objects by its given uids
	 *
	 * @param string $uids commaseparated list of uids
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findByUids($uids) {
		$query = $this->createQuery();
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE)
			->setRespectSysLanguage(FALSE);

		$query->matching(
			$query->in('uid', GeneralUtility::intExplode(',', $uids, TRUE))
		);

		$result = $query->execute();
		return $result;
	}

	/**
	 * Find Form by given Page Uid
	 *
	 * @param int $uid page uid
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findByPages($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE)
			->setRespectSysLanguage(FALSE);

		$query->matching(
			$query->equals('pages.uid', $uid)
		);

		$result = $query->execute()->getFirst();
		return $result;
	}

	/**
	 * Returns form with captcha from given UID
	 *
	 * @param Form $form
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function hasCaptcha(Form $form) {
		$query = $this->createQuery();
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE)
			->setRespectSysLanguage(FALSE);
		$and = array(
			$query->equals('uid', $form->getUid()),
			$query->equals('pages.fields.type', 'captcha')
		);
		$query->matching(
			$query->logicalAnd($and)
		);
		return $query->execute();
	}

	/**
	 * Returns form with password from given UID
	 *
	 * @param Form $form
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function hasPassword(Form $form) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$and = array(
			$query->equals('uid', $form->getUid()),
			$query->equals('pages.fields.type', 'password')
		);
		$query->matching(
			$query->logicalAnd($and)
		);
		return $query->execute();
	}

	/**
	 * This function is a workarround to get the field value of
	 * "pages" in the table "forms"
	 * (only relevant if IRRE was replaced by Element Browser)
	 *
	 * @param int $uid Form UID
	 * @return string
	 */
	public function getPagesValue($uid) {
		$query = $this->createQuery();

		// create sql statement
		$sql = 'select pages';
		$sql .= ' from tx_powermail_domain_model_forms';
		$sql .= ' where uid = ' . intval($uid);
		$sql .= ' limit 1';

		$result = $query->statement($sql)->execute(TRUE);

		return $result[0]['pages'];
	}

	/**
	 * Find all and don't respect Storage
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAll() {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		return $query->execute();
	}

	/**
	 * Find all within a Page and its subpages
	 *
	 * @param int $pid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllInPid($pid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		if ($pid > 0) {
			/** @var \TYPO3\CMS\Core\Database\QueryGenerator $queryGenerator */
			$queryGenerator = GeneralUtility::makeInstance('TYPO3\CMS\Core\Database\QueryGenerator');
			$pidList = $queryGenerator->getTreeList($pid, 20, 0, 1);
			$query->matching(
				$query->in('pid', GeneralUtility::trimExplode(',', $pidList, TRUE))
			);
		}

		return $query->execute();
	}

	/**
	 * Find all old powermail forms in database
	 *
	 * @return mixed
	 */
	public function findAllOldForms() {
		if (!$this->oldPowermailTablesExists()) {
			return array();
		}
		$query = $this->createQuery();

		$sql = 'select c.*';
		$sql .= ' from tx_powermail_fields f
			left join tx_powermail_fieldsets fs ON f.fieldset = fs.uid
			left join tt_content c ON c.uid = fs.tt_content
		';
		$sql .= ' where c.deleted = 0';
		$sql .= ' group by c.uid';
		$sql .= ' order by c.sys_language_uid, c.uid';
		$sql .= ' limit 10000';

		$result = $query->statement($sql)->execute(TRUE);

		return $result;
	}

	/**
	 * @param int $uid tt_content uid
	 * @return array
	 */
	public function findOldFieldsetsAndFieldsToTtContentRecord($uid) {
		$query = $this->createQuery();

		$sql = 'select
			fs.uid,
			fs.pid,
			fs.sys_language_uid,
			fs.l18n_parent,
			fs.sorting,
			fs.hidden,
			fs.title,
			fs.class
		';
		$sql .= ' from tx_powermail_fieldsets fs
			left join tt_content c ON c.uid = fs.tt_content
		';
		$sql .= ' where c.deleted = 0 and fs.deleted = 0 and c.uid = ' . intval($uid);
		$sql .= ' group by fs.uid';
		$sql .= ' order by fs.sys_language_uid, fs.uid';
		$sql .= ' limit 10000';

		$fieldsets = $query->statement($sql)->execute(TRUE);

		$result = array();
		$counter = 0;
		foreach ($fieldsets as $fieldset) {
			$result[$counter] = $fieldset;
			$result[$counter]['_fields'] = $this->findOldFieldsToFieldset($fieldset['uid']);
			$counter++;
		}

		return $result;
	}

	/**
	 * @param int $uid Fieldset
	 * @return array
	 */
	protected function findOldFieldsToFieldset($uid) {
		$query = $this->createQuery();

		$sql = 'select
			f.uid,
			f.pid,
			f.sys_language_uid,
			f.l18n_parent,
			f.sorting,
			f.hidden,
			f.fe_group,
			f.fieldset,
			f.title,
			f.formtype,
			f.flexform,
			f.fe_field,
			f.name,
			f.description,
			f.class
		';
		$sql .= ' from tx_powermail_fields f
			left join tx_powermail_fieldsets fs ON f.fieldset = fs.uid
			left join tt_content c ON c.uid = fs.tt_content
		';
		$sql .= ' where c.deleted = 0 and fs.deleted = 0 and f.deleted = 0 and fs.uid = ' . intval($uid);
		$sql .= ' group by f.uid';
		$sql .= ' order by f.sys_language_uid, f.uid';
		$sql .= ' limit 10000';

		$fields = $query->statement($sql)->execute(TRUE);

		foreach ($fields as $key => $field) {
			$subValues = GeneralUtility::xml2array($field['flexform']);
			$fields[$key]['size'] = $this->getFlexFormValue($subValues, 'size');
			$fields[$key]['maxlength'] = $this->getFlexFormValue($subValues, 'maxlength');
			$fields[$key]['readonly'] = $this->getFlexFormValue($subValues, 'readonly');
			$fields[$key]['mandatory'] = $this->getFlexFormValue($subValues, 'mandatory');
			$fields[$key]['value'] = $this->getFlexFormValue($subValues, 'value');
			$fields[$key]['placeholder'] = $this->getFlexFormValue($subValues, 'placeholder');
			$fields[$key]['validate'] = $this->getFlexFormValue($subValues, 'validate');
			$fields[$key]['pattern'] = $this->getFlexFormValue($subValues, 'pattern');
			$fields[$key]['inputtype'] = $this->getFlexFormValue($subValues, 'inputtype');
			$fields[$key]['options'] = $this->getFlexFormValue($subValues, 'options');
			$fields[$key]['path'] = $this->getFlexFormValue($subValues, 'typoscriptobject');
			$fields[$key]['multiple'] = $this->getFlexFormValue($subValues, 'multiple');
			unset($fields[$key]['flexform']);
		}

		return $fields;
	}

	/**
	 * Find all localized records with
	 * 		tx_powermail_domain_model_forms.pages = ""
	 *
	 * @return mixed
	 */
	public function findAllWrongLocalizedForms() {
		$query = $this->createQuery();

		$sql = 'select uid,pid,title';
		$sql .= ' from tx_powermail_domain_model_forms';
		$sql .= ' where pages = ""';
		$sql .= ' and sys_language_uid > 0';
		$sql .= ' and deleted = 0';
		$sql .= ' limit 1';

		$result = $query->statement($sql)->execute(TRUE);

		return $result;
	}

	/**
	 * Fix wrong localized forms
	 *
	 * @return void
	 */
	public function fixWrongLocalizedForms() {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_powermail_domain_model_forms',
			'sys_language_uid > 0 and deleted = 0 and pages = ""',
			array('pages' => 0)
		);
	}

	/**
	 * @param $xmlArray
	 * @param $key
	 * @return string
	 */
	protected function getFlexFormValue($xmlArray, $key) {
		if (is_array($xmlArray) && isset($xmlArray['data']['sDEF']['lDEF'][$key]['vDEF'])) {
			if (!empty($xmlArray['data']['sDEF']['lDEF'][$key]['vDEF'])) {
				return $xmlArray['data']['sDEF']['lDEF'][$key]['vDEF'];
			}
		}
		return '';
	}

	/**
	 * Check if old powermail tables existing
	 *
	 * @return bool
	 */
	protected function oldPowermailTablesExists() {
		$allTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		if (array_key_exists('tx_powermail_fields', $allTables) && array_key_exists('tx_powermail_fieldsets', $allTables)) {
			return TRUE;
		}
		return FALSE;
	}
}