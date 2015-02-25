<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
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
 * Converts old to new forms
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FormConverter {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Configuration
	 *
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * Dryrun for testing
	 *
	 * @var bool
	 */
	protected $dryrun = FALSE;

	/**
	 * Delete Old Forms
	 *
	 * @var bool
	 */
	protected $deleteOldForms = TRUE;

	/**
	 * Result for output
	 *
	 * @var array
	 */
	protected $result = array();

	/**
	 * Old to New array for localization
	 *
	 * @var array
	 */
	protected $localizationRelations = array(
		'content' => array(),
		'form' => array(),
		'page' => array(),
		'field' => array()
	);

	/**
	 * Create new forms from old ones
	 *
	 * @param array $oldFormsWithFieldsetsAndFields
	 * @param array $configuration
	 * @return array result
	 */
	public function createNewFromOldForms($oldFormsWithFieldsetsAndFields, $configuration) {
		$this->configuration = $configuration;
		if (!empty($this->configuration['dryrun'])) {
			$this->setDryrun(TRUE);
		}
		if (!$this->getDryrun()) {
			GeneralUtility::devLog(
				'Old Forms to convert',
				'powermail',
				0,
				$oldFormsWithFieldsetsAndFields
			);
		}

		// create forms and content
		$formCounter = 0;
		foreach ((array) $oldFormsWithFieldsetsAndFields as $form) {
			// ignore hidden forms
			if ($form['hidden'] === '1' && $this->configuration['hidden'] === '1') {
				continue;
			}
			$formUid = $this->createFormRecord($form, $formCounter);
			$ttContentIdNew = $this->createTtContentRecord ( $form, $formUid );
			$this->updateTvMapping($form['pid'], $form['uid'], $ttContentIdNew);
			$formCounter++;
		}

		// delete old forms and content
		$this->deleteOldRecords($oldFormsWithFieldsetsAndFields);

		return $this->result;
	}

	/**
	 * Update templavoila mapping if new tt_content was created
	 *
	 * @param int $pid
	 * @param int $uidOld
	 * @param int $uidNew
	 * @return void
	 */
	protected function updateTvMapping($pid, $uidOld, $uidNew) {
		if (!ExtensionManagementUtility::isLoaded('templavoila') || $this->getDryrun()) {
			return;
		}
		if ($uidOld > 1 && $uidNew > 1) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_templavoila_flex', 'pages', 'uid = ' . intval($pid), '', '', 1);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			$flex = preg_replace_callback(
				'~>(\S*)<~',
				function($matches) use ($uidOld, $uidNew) {
					$uids = explode(',', $matches[1]);
					foreach ($uids as $key => $uid) {
						if ($uid === $uidOld) {
							$uids[$key] = $uidNew;
						}
					}
					return '>' . implode(',', $uids) . '<';
				},
				$row['tx_templavoila_flex']
			);

			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'pages',
				'uid = ' . intval($pid),
				array(
					'tx_templavoila_flex' => $flex
				)
			);
		}
	}

	/**
	 * Create tt_content record
	 *
	 * @param array $form
	 * @param int $formUid Form that was created before
	 * @return int tt_content uid
	 */
	protected function createTtContentRecord($form, $formUid) {
		$ignoreFields = array(
			'uid',
			'tstamp',
			'cruser_id',
			'CType',
			'_fieldsets',
			'l18n_parent',
			'l18n_diffsource'
		);
		$ttContentProperties = array();
		foreach ($form as $tableColumn => $tableValue) {
			if (!in_array($tableColumn, $ignoreFields)) {
				$ttContentProperties[$tableColumn] = $tableValue;
			}
		}
		$ttContentProperties['pid'] = $form['pid'];
		$ttContentProperties['list_type'] = 'powermail_pi1';
		$ttContentProperties['CType'] = 'list';
		$ttContentProperties['tstamp'] = time();
		$ttContentProperties['pi_flexform'] = $this->createFlexForm($form, $formUid);
		if ($form['sys_language_uid'] > 0 && !empty($this->localizationRelations['content'][$form['l18n_parent']])) {
			$ttContentProperties['l18n_parent'] = $this->localizationRelations['content'][$form['l18n_parent']];
		}
		if (!$this->getDryrun()) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_content', $ttContentProperties);
			$ttContentUid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$this->localizationRelations['content'][$form['uid']] = $ttContentUid;
			return $ttContentUid;
		}
		return 0;
	}

	/**
	 * Create Form Record
	 *
	 * @param array $form
	 * @param int $formCounter
	 * @return int $formUid
	 */
	protected function createFormRecord($form, $formCounter) {
		$formProperties = array(
			'uid' => 0,
			'pid' => ($this->configuration['save'] === '[samePage]' ? $form['pid'] : intval($this->configuration['save'])),
			'title' => $form['tx_powermail_title'],
			'pages' => $form['tx_powermail_fieldsets'],
			'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
			'hidden' => $form['hidden'],
			'crdate' => time(),
			'tstamp' => time()
		);
		if ($form['sys_language_uid'] > 0) {
			$formProperties['sys_language_uid'] = $form['sys_language_uid'];
			$formProperties['l10n_parent'] = $this->localizationRelations['form'][$form['l18n_parent']];
		}
		if (!$this->getDryrun()) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_domain_model_forms', $formProperties);
			$formProperties['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$this->localizationRelations['form'][$form['uid']] = $formProperties['uid'];
		}
		$this->result[$formCounter] = $formProperties;

		// create pages
		$pageCounter = 0;
		foreach ((array) $form['_fieldsets'] as $page) {
			$this->createPageRecord($form, $page, $formProperties['uid'], $formCounter, $pageCounter);
			$pageCounter++;
		}
		return $formProperties['uid'];
	}

	/**
	 * Create Page Record
	 *
	 * @param array $form
	 * @param array $page
	 * @param int $formUid
	 * @param int $formCounter
	 * @param int $pageCounter
	 * @return void
	 */
	protected function createPageRecord($form, $page, $formUid, $formCounter, $pageCounter) {
		$pageProperties = array(
			'uid' => 0,
			'pid' => ($this->configuration['save'] === '[samePage]' ? $form['pid'] : intval($this->configuration['save'])),
			'forms' => $formUid,
			'title' => $page['title'],
			'css' => $this->getValueIfDefaultLanguage($page, 'class'),
			'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
			'hidden' => $page['hidden'],
			'tstamp' => time(),
			'crdate' => time(),
			'sorting' => $page['sorting']
		);
		if ($page['sys_language_uid'] > 0) {
			$pageProperties['sys_language_uid'] = $page['sys_language_uid'];
			$pageProperties['l10n_parent'] = $this->localizationRelations['page'][$page['l18n_parent']];
//			unset($pageProperties['forms']);
		}
		if (!$this->getDryrun()) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_domain_model_pages', $pageProperties);
			$pageProperties['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$this->localizationRelations['page'][$page['uid']] = $pageProperties['uid'];
		}
		$this->result[$formCounter]['_pages'][$pageCounter] = $pageProperties;

		// create fields
		$fieldCounter = 0;
		foreach ((array) $page['_fields'] as $field) {
			if (!$this->rewriteFormType($field)) {
				continue;
			}
			$this->createFieldRecord($form, $pageProperties['uid'], $field, $formCounter, $pageCounter, $fieldCounter);
			$fieldCounter++;
		}
	}

	/**
	 * Create Field Record
	 *
	 * @param array $form
	 * @param int $pageUid
	 * @param array $field
	 * @param int $formCounter
	 * @param int $pageCounter
	 * @param int $fieldCounter
	 * @return void
	 */
	protected function createFieldRecord($form, $pageUid, $field, $formCounter, $pageCounter, $fieldCounter) {
		$fieldProperties = array(
			'uid' => 0,
			'pid' => ($this->configuration['save'] === '[samePage]' ? $form['pid'] : intval($this->configuration['save'])),
			'pages' => $pageUid,
			'title' => $field['title'],
			'type' => $this->rewriteFormType($field),
			'css' => $this->rewriteStyles($field),
			'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
			'hidden' => $field['hidden'],
			'sorting' => $field['sorting'],
			'marker' => $this->getMarker($field),
			'settings' => $field['options'],
			'path' => $this->getValueIfDefaultLanguage($field, 'path'),
			'content_element' => $field['path'],
			'text' => $field['value'],
			'placeholder' => $field['placeholder'],
			'description' => $field['description'],
			'prefill_value' => $this->getPrefillValue($field),
			'feuser_value' => $this->getValueIfDefaultLanguage($field, 'fe_field'),
			'mandatory' => $this->getValueIfDefaultLanguage($field, 'mandatory'),
			'validation' => $this->rewriteValidation($field),
			'validation_configuration' => $this->rewriteValidationConfiguration($field),
			'datepicker_settings' => $this->getDatePickerSettings($field),
			'multiselect' => $this->getValueIfDefaultLanguage($field, 'multiple'),
			'sender_email' => $this->isSenderEmail($form, $field),
			'sender_name' => $this->isSenderName($form, $field),
			'tstamp' => time(),
			'crdate' => time()
		);
		if ($field['sys_language_uid'] > 0) {
			$fieldProperties['sys_language_uid'] = $field['sys_language_uid'];
			$fieldProperties['l10n_parent'] = $this->localizationRelations['field'][$field['l18n_parent']];
//			unset($fieldProperties['pages']);
		}
		if (!$this->getDryrun()) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_powermail_domain_model_fields', $fieldProperties);
			$fieldProperties['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$this->localizationRelations['field'][$field['uid']] = $fieldProperties['uid'];
		}
		$this->result[$formCounter]['_pages'][$pageCounter]['_fields'][$fieldCounter] = $fieldProperties;
	}

	/**
	 * Create FlexForm
	 *
	 * @param array $form
	 * @param int $formUid Form that was created before
	 * @return string
	 */
	protected function createFlexForm($form, $formUid) {
		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
		);
		$templatePathAndFilename = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
		$templatePathAndFilename .= 'Module/ConverterFlexForm.xml';
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		$view->getRequest()->setControllerExtensionName('Powermail');
		$view->getRequest()->setPluginName('Pi1');
		$view->getRequest()->setControllerName('Module');
		$view->setTemplatePathAndFilename($templatePathAndFilename);

		// manipulate variables
		$form['tx_powermail_thanks'] = $this->rewriteVariables($form['tx_powermail_thanks'], $form, TRUE);
		$form['tx_powermail_mailsender'] = $this->rewriteVariables($form['tx_powermail_mailsender'], $form, TRUE);
		$form['tx_powermail_mailreceiver'] = $this->rewriteVariables($form['tx_powermail_mailreceiver'], $form, TRUE);
		$form['tx_powermail_recipient'] = $this->rewriteVariables($form['tx_powermail_recipient'], $form);
		$form['tx_powermail_subject_r'] = $this->rewriteVariables($form['tx_powermail_subject_r'], $form);
		$form['tx_powermail_subject_s'] = $this->rewriteVariables($form['tx_powermail_subject_s'], $form);
		if ($form['sys_language_uid'] > 0) {
			$formUid = $this->localizationRelations['form'][$form['l18n_parent']];
		}
		$view->assignMultiple(
			array(
				'formUid' => $formUid,
				'form' => $form,
				'configuration' => $this->configuration
			)
		);

		return $view->render();
	}

	/**
	 * Set flag to deleted=1 for old stuff
	 *
	 * @param array $oldFormsWithFieldsetsAndFields
	 * @return void
	 */
	protected function deleteOldRecords($oldFormsWithFieldsetsAndFields) {
		if ($this->getDryrun() || !$this->deleteOldForms) {
			return;
		}
		foreach ($oldFormsWithFieldsetsAndFields as $ttContent) {
			// ignore hidden forms
			if ($ttContent['hidden'] === '1' && $this->configuration['hidden'] === '1') {
				continue;
			}
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'uid = ' . $ttContent['uid'], array('deleted' => 1));
			foreach ($ttContent['_fieldsets'] as $fieldset) {
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_powermail_fieldsets', 'uid = ' . $fieldset['uid'], array('deleted' => 1));
				foreach ($fieldset['_fields'] as $field) {
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_powermail_fields', 'uid = ' . $field['uid'], array('deleted' => 1));
				}
			}
		}
	}

	/**
	 * Contains this field the sendermail?
	 *
	 * @param array $form
	 * @param array $field
	 * @return string
	 */
	protected function isSenderEmail($form, $field) {
		if ($field['sys_language_uid'] > 0) {
			return '0';
		}
		if ($form['tx_powermail_sender'] === 'uid' . $field['uid']) {
			return '1';
		}
		return '0';
	}

	/**
	 * Contains this field the sendername?
	 *
	 * @param array $form
	 * @param array $field
	 * @return string
	 */
	protected function isSenderName($form, $field) {
		if ($field['sys_language_uid'] > 0) {
			return '0';
		}
		$senderNameFields = GeneralUtility::trimExplode(',', $form['tx_powermail_sendername'], TRUE);
		foreach ($senderNameFields as $senderNameField) {
			if ($senderNameField === 'uid' . $field['uid']) {
				return '1';
			}
		}
		return '0';
	}

	/**
	 * get datepicker settings
	 *
	 * @param array $field
	 * @return int
	 */
	protected function getDatePickerSettings($field) {
		if ($field['sys_language_uid'] > 0) {
			return '0';
		}
		if ($field['formtype'] === 'date') {
			return 'date';
		}
		if ($field['formtype'] === 'datetime') {
			return 'datetime';
		}
		if ($field['inputtype'] === 'time') {
			return 'time';
		}
		return '';
	}

	/**
	 * Reformat fieldtypes from old to new
	 *
	 * @param array $field
	 * @return bool|string
	 */
	protected function rewriteFormType($field) {
		if ($field['inputtype'] === 'time') {
			return 'date';
		}

		$formTypes = array(
			'text' => 'input',
			'textarea' => 'textarea',
			'select' => 'select',
			'check' => 'check',
			'radio' => 'radio',
			'submit' => 'submit',
			'captcha' => 'captcha',
			'reset' => 'reset',
			'label' => 'text',
			'content' => 'content',
			'html' => 'html',
			'password' => 'password',
			'file' => 'file',
			'hidden' => 'hidden',
			'datetime' => 'date',
			'date' => 'date',
			'button' => FALSE,
			'submitgraphic' => 'submit',
			'countryselect' => 'country',
			'typoscript' => 'typoscript'
		);
		if (array_key_exists($field['formtype'], $formTypes)) {
			return $formTypes[$field['formtype']];
		}

		return FALSE;
	}

	/**
	 * Reformat styles
	 *
	 * @param array $field
	 * @return string
	 */
	protected function rewriteStyles($field) {
		if ($field['sys_language_uid'] > 0) {
			return '';
		}

		$styleTypes = array(
//			'style1' => 'layout1',
			'style2' => 'layout2',
			'style3' => 'layout3'
		);
		if (array_key_exists($field['class'], $styleTypes)) {
			return $styleTypes[$field['class']];
		}
		return '';
	}

	/**
	 * Reformat validation
	 *
	 * @param array $field
	 * @return string
	 */
	protected function rewriteValidation($field) {
		if ($field['sys_language_uid'] > 0) {
			return '';
		}

		$newStrings = array(
			'validate-email' => '1',
			'validate-url' => '2',
			'validate-number' => '4',
			'validate-digits' => '',
			'validate-alpha' => '5',
			'validate-alphanum' => '',
			'validate-pattern' => '10',
			'validate-alpha-w-umlaut' => '5',
			'validate-alphanum-w-umlaut' => ''
		);
		if (array_key_exists($field['validate'], $newStrings)) {
			return $newStrings[$field['validate']];
		}

		$newStrings = array(
			'color' => '',
			'range' => '8',
			'tel' => '3',
			'time' => ''
		);
		if (array_key_exists($field['inputtype'], $newStrings)) {
			return $newStrings[$field['inputtype']];
		}

		return '';
	}

	/**
	 * @param array $field
	 * @return string
	 */
	protected function rewriteValidationConfiguration($field) {
		if ($field['sys_language_uid'] > 0) {
			return '';
		}

		if ($field['validate'] === 'validate-pattern') {
			if (!empty($field['pattern'])) {
				return $field['pattern'];
			}
		}
		if ($field['inputtype'] === 'range') {
			return '0,10';
		}
		return '';
	}

	/**
	 * Prefill value
	 *
	 * @param array $field
	 * @return string
	 */
	protected function getPrefillValue($field) {
		if ($field['inputtype'] === 'time' || $field['formtype'] === 'date' || $field['formtype'] === 'datetime') {
			return '';
		}
		return $field['value'];
	}

	/**
	 * Convert old to new marker
	 * 		from: this is the ###uid123### value
	 * 		to: this is the {uid123} value
	 *
	 * @param string $string
	 * @param array $form
	 * @param bool $rte
	 * @return string
	 */
	protected function rewriteVariables($string, $form, $rte = FALSE) {
		$string = str_replace('###POWERMAIL_ALL###', '{powermail_all}', $string);
		$string = preg_replace_callback(
			'|###UID([^#]*)?###|i',
			function($matches) use ($form) {
				$uid = $matches[1];
				if ($form['sys_language_uid'] > 0) {
					$uid = FormConverter::getDefaultUidFromOldLocalizedFieldUid($form, $uid);
				}
				return '{uid' . $uid . '}';
			},
			$string
		);
		if ($rte && !empty($this->configuration['parseFunc'])) {
			$this->initialiazeTsfe();
			$typoScriptSetup = $this->configurationManager->getConfiguration(
				\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
			);
			$parseFunc = $typoScriptSetup['lib.'][$this->configuration['parseFunc']];
			$contentObject = $this->configurationManager->getContentObject();
			$string = $contentObject->_parseFunc($string, $parseFunc);
		}
		return $string;
	}

	/**
	 * Initialize TSFE object
	 *
	 * @return void
	 */
	protected function initialiazeTsfe() {
		if (!is_object($GLOBALS['TT'])) {
			$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\TimeTracker;
			$GLOBALS['TT']->start();
		}
		if (!is_object($GLOBALS['TSFE'])) {
			$id = (GeneralUtility::_GP('id') ? GeneralUtility::_GP('id') : 1);
			$GLOBALS['TSFE'] = new \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController(
				$GLOBALS['TYPO3_CONF_VARS'], $id, 0, 0, 0, 0, 0, 0
			);
			$GLOBALS['TSFE']->tmpl = GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\ExtendedTemplateService');
			$GLOBALS['TSFE']->sys_page = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
			$GLOBALS['TSFE']->tmpl->tt_track = 0;
			$GLOBALS['TSFE']->tmpl->init();
			$rootLine = $GLOBALS['TSFE']->sys_page->getRootLine($id);
			$GLOBALS['TSFE']->tmpl->runThroughTemplates($rootLine);
			$GLOBALS['TSFE']->tmpl->generateConfig();
			$GLOBALS['TSFE']->tmpl->loaded = 1;
			$GLOBALS['TSFE']->getConfigArray();
			$GLOBALS['TSFE']->linkVars = '' . $GLOBALS['TSFE']->config['config']['linkVars'];
			if ($GLOBALS['TSFE']->config['config']['simulateStaticDocuments_pEnc_onlyP']) {
				$ssd = GeneralUtility::trimExplode(',', $GLOBALS['TSFE']->config['config']['simulateStaticDocuments_pEnc_onlyP'], TRUE);
				foreach ($ssd as $tempP) {
					$GLOBALS['TSFE']->pEncAllowedParamNames[$tempP] = 1;
				}
			}
		}
	}

	/**
	 * Rewrite old UID markers
	 *
	 * @param string $form
	 * @param int $oldUid
	 * @return int
	 */
	public static function getDefaultUidFromOldLocalizedFieldUid($form, $oldUid) {
		foreach ($form['_fieldsets'] as $fieldset) {
			foreach ($fieldset['_fields'] as $field) {
				if ($oldUid === $field['uid']) {
					return $field['l18n_parent'];
				}
			}
		}
		return 0;
	}

	/**
	 * Return value only if default language
	 *
	 * @param array $array
	 * @param string $key
	 * @return string
	 */
	protected function getValueIfDefaultLanguage($array, $key) {
		if ($array['sys_language_uid'] > 0) {
			return '';
		}
		return $array[$key];
	}

	/**
	 * Create marker
	 *
	 * @param array $field
	 * @return string
	 */
	protected function getMarker($field) {
		if ($field['sys_language_uid'] > 0) {
			return '';
		}
		return 'uid' . $field['uid'];
	}

	/**
	 * @param boolean $dryrun
	 * @return void
	 */
	public function setDryrun($dryrun) {
		$this->dryrun = $dryrun;
	}

	/**
	 * @return boolean
	 */
	public function getDryrun() {
		return $this->dryrun;
	}
}
