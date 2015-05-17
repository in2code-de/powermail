<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use In2code\Powermail\Domain\Model\Mail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Div is a class for a collection of misc functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *            GNU Lesser General Public License, version 3 or later
 */
class Div {

	/**
	 * Extension Key
	 */
	public static $extKey = 'powermail';

	/**
	 * @var \In2code\Powermail\Domain\Repository\FormRepository
	 * @inject
	 */
	protected $formRepository;

	/**
	 * @var \In2code\Powermail\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * @var \In2code\Powermail\Domain\Repository\MailRepository
	 * @inject
	 */
	protected $mailRepository;

	/**
	 * @var \In2code\Powermail\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository;

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
	 * Get Field Uid List from given Form Uid
	 *
	 * @param \integer $formUid Form Uid
	 * @return \array
	 */
	public function getFieldsFromForm($formUid) {
		$allowedFieldTypes = array(
			'input',
			'textarea',
			'select',
			'check',
			'radio',
			'password',
			'file',
			'hidden',
			'date',
			'location',
			'typoscript'
		);

		$fields = array();
		$form = $this->formRepository->findByUid($formUid);
		if (!method_exists($form, 'getPages')) {
			return array();
		}
		foreach ($form->getPages() as $page) {
			foreach ($page->getFields() as $field) {
				// skip type submit
				if (!in_array($field->getType(), $allowedFieldTypes)) {
					continue;
				}
				$fields[] = $field->getUid();
			}
		}

		return $fields;
	}

	/**
	 * Returns sendername from a couple of arguments
	 *
	 * @param Mail $mail Given Params
	 * @param string $default
	 * @param string $glue
	 * @return string Sender Name
	 */
	public function getSenderNameFromArguments(Mail $mail, $default = NULL, $glue = ' ') {
		$name = '';
		foreach ($mail->getAnswers() as $answer) {
			if (method_exists($answer->getField(), 'getUid') && $answer->getField()->getSenderName()) {
				if (!is_array($answer->getValue())) {
					$value = $answer->getValue();
				} else {
					$value = implode($glue, $answer->getValue());
				}
				$name .= $value . $glue;
			}
		}

		if (!trim($name) && $default) {
			$name = $default;
		}

		if (empty($name) && !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
			$name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
		}

		if (!trim($name)) {
			$name = LocalizationUtility::translate('error_no_sender_name', 'powermail');
		}
		return trim($name);
	}

	/**
	 * Returns senderemail from a couple of arguments
	 *
	 * @param Mail $mail
	 * @param string $default
	 * @return string Sender Email
	 */
	public function getSenderMailFromArguments(Mail $mail, $default = NULL) {
		$email = '';
		foreach ($mail->getAnswers() as $answer) {
			if (
				method_exists($answer->getField(), 'getUid') &&
				$answer->getField()->getSenderEmail() &&
				GeneralUtility::validEmail($answer->getValue())
			) {
				$email = $answer->getValue();
				break;
			}
		}

		if (empty($email) && $default) {
			$email = $default;
		}

		if (empty($email) && GeneralUtility::validEmail($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
			$email = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
		}

		if (empty($email)) {
			$email = LocalizationUtility::translate('error_no_sender_email', 'powermail');
			$email .= '@';
			$email .= str_replace('www.', '', GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'));
		}
		return $email;
	}

	/**
	 * Save current timestamp to session
	 *
	 * @param QueryResult $forms
	 * @param array $settings
	 * @return void
	 */
	public static function saveFormStartInSession($forms, array $settings) {
		$form = $forms->getFirst();
		if ($form !== NULL && self::sessionCheckEnabled($settings)) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermailFormstart' . $form->getUid(), time());
			$GLOBALS['TSFE']->storeSessionData();
		}
	}

	/**
	 * Read FormStart
	 *
	 * @param integer $formUid Form UID
	 * @param array $settings
	 * @return integer Timestamp
	 */
	public static function getFormStartFromSession($formUid, array $settings) {
		if (self::sessionCheckEnabled($settings)) {
			return $GLOBALS['TSFE']->fe_user->getKey('ses', 'powermailFormstart' . $formUid);
		}
		return 0;
	}

	/**
	 * Check if spamshield and sessioncheck is enabled
	 *
	 * @param array $settings
	 * @return bool
	 */
	protected static function sessionCheckEnabled(array $settings) {
		$settings = GeneralUtility::removeDotsFromTS($settings);
		if (!empty($settings['spamshield']['_enable']) && !empty($settings['spamshield']['indicator']['session'])) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns given number or the current PID
	 *
	 * @param integer $pid Storage PID or nothing
	 * @return integer $pid
	 */
	public static function getStoragePage($pid = 0) {
		if (!$pid) {
			$pid = $GLOBALS['TSFE']->id;
		}
		return $pid;
	}

	/**
	 * This functions renders the powermail_all Template (e.g. useage in Mails)
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param string $section Choose a section (web or mail)
	 * @param array $settings TypoScript Settings
	 * @param string $type "createAction", "confirmationAction", "sender", "receiver"
	 * @return string content parsed from powermailAll HTML Template
	 */
	public function powermailAll(Mail $mail, $section = 'web', $settings = array(), $type = NULL) {
		/** @var \In2code\Powermail\Utility\StandaloneViewMultiplePaths $powermailAll */
		$powermailAll = $this->objectManager->get('In2code\\Powermail\\Utility\\StandaloneViewMultiplePaths');
		$templatePathAndFilename = $this->getTemplatePath('Form/PowermailAll.html');
		$powermailAll->setTemplatePathAndFilename($templatePathAndFilename);
		$powermailAll->setLayoutRootPaths($this->getTemplateFolders('layout'));
		$powermailAll->setPartialRootPaths($this->getTemplateFolders('partial'));
		$powermailAll->assignMultiple(
			array(
				'mail' => $mail,
				'section' => $section,
				'settings' => $settings,
				'type' => $type
			)
		);
		return $powermailAll->render();
	}

	/**
	 * Get absolute path for templates with fallback
	 * 		In case of multiple paths this will just return the first one.
	 * 		See getTemplateFolders() for an array of paths.
	 *
	 * @param string $part "template", "partial", "layout"
	 * @return string
	 * @see getTemplateFolders()
	 */
	public function getTemplateFolder($part = 'template') {
		$matches = $this->getTemplateFolders($part);
		return !empty($matches) ? $matches[0] : '';
	}

	/**
	 * Get absolute paths for templates with fallback
	 * 		Returns paths from *RootPaths and *RootPath and "hardcoded"
	 * 		paths pointing to the EXT:powermail-resources.
	 *
	 * @param string $part "template", "partial", "layout"
	 * @param boolean $returnAllPaths Default: FALSE, If FALSE only paths
	 * 		for the first configuration (Paths, Path, hardcoded)
	 * 		will be returned. If TRUE all (possible) paths will be returned.
	 * @return array
	 */
	public function getTemplateFolders($part = 'template', $returnAllPaths = FALSE) {
		$templatePaths = array();
		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
		);
		if (!empty($extbaseFrameworkConfiguration['view'][$part . 'RootPaths'])) {
			$templatePaths = $extbaseFrameworkConfiguration['view'][$part . 'RootPaths'];
			krsort($templatePaths);
			$templatePaths = array_values($templatePaths);
		}
		if ($returnAllPaths || empty($templatePaths)) {
			$path = $extbaseFrameworkConfiguration['view'][$part . 'RootPath'];
			if (!empty($path)) {
				$templatePaths[] = $path;
			}
		}
		if ($returnAllPaths || empty($templatePaths)) {
			$templatePaths[] = 'EXT:powermail/Resources/Private/' . ucfirst($part) . 's/';
		}
		$templatePaths = array_unique($templatePaths);
		$absoluteTemplatePaths = array();
		foreach ($templatePaths as $templatePath) {
			$absoluteTemplatePaths[] = GeneralUtility::getFileAbsFileName($templatePath);
		}
		return $absoluteTemplatePaths;
	}

	/**
	 * Return path and filename for a file or path.
	 * 		Only the first existing file/path will be returned.
	 * 		respect *RootPaths and *RootPath
	 *
	 * @param string $relativePathAndFilename e.g. Email/Name.html
	 * @param string $part "template", "partial", "layout"
	 * @return string Filename/path
	 */
	public function getTemplatePath($relativePathAndFilename, $part = 'template') {
		$matches = $this->getTemplatePaths($relativePathAndFilename, $part);
		return !empty($matches) ? $matches[0] : '';
	}

	/**
	 * Return path and filename for one or many files/paths.
	 * 		Only existing files/paths will be returned.
	 * 		respect *RootPaths and *RootPath
	 *
	 * @param string $relativePathAndFilename Path/filename (Email/Name.html) or path
	 * @param string $part "template", "partial", "layout"
	 * @return array All existing matches found
	 */
	public function getTemplatePaths($relativePathAndFilename, $part = 'template') {
		$absolutePathAndFilenameMatches = array();
		$absolutePaths = $this->getTemplateFolders($part, TRUE);
		foreach ($absolutePaths as $absolutePath) {
			if (file_exists($absolutePath . $relativePathAndFilename)) {
				$absolutePathAndFilenameMatches[] = $absolutePath . $relativePathAndFilename;
			}
		}
		return $absolutePathAndFilenameMatches;
	}

	/**
	 * Generate a new array with markers and their values
	 *        firstname => value
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return array
	 */
	public function getVariablesWithMarkersFromMail(Mail $mail) {
		$variables = array();
		foreach ($mail->getAnswers() as $answer) {
			if (!method_exists($answer, 'getField') || !method_exists($answer->getField(), 'getMarker')) {
				continue;
			}
			$value = $answer->getValue();
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			$variables[$answer->getField()->getMarker()] = $value;
		}
		return $variables;
	}

	/**
	 * Generate a new array with labels
	 *        label_firstname => Firstname
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return array
	 */
	public function getLabelsWithMarkersFromMail(Mail $mail) {
		$variables = array();
		foreach ($mail->getAnswers() as $answer) {
			if (!method_exists($answer, 'getField') || !method_exists($answer->getField(), 'getMarker')) {
				continue;
			}
			$variables['label_' . $answer->getField()->getMarker()] = $answer->getField()->getTitle();
		}
		return $variables;
	}

	/**
	 * Return uid from given field marker and form
	 *
	 * @param string $marker Field marker
	 * @param integer $formUid Form UID
	 * @return int Field UID
	 */
	public function getFieldUidFromMarker($marker, $formUid = 0) {
		$field = $this->fieldRepository->findByMarkerAndForm($marker, $formUid);
		if (method_exists($field, 'getUid')) {
			return $field->getUid();
		}
		return 0;
	}

	/**
	 * Return type from given field marker and form
	 *
	 * @param string $marker Field marker
	 * @param integer $formUid Form UID
	 * @return string Field Type
	 */
	public function getFieldTypeFromMarker($marker, $formUid = 0) {
		$field = $this->fieldRepository->findByMarkerAndForm($marker, $formUid);
		if (method_exists($field, 'getType')) {
			return $field->getType();
		}
		return '';
	}

	/**
	 * Return expected value type from fieldtype
	 *
	 * @param string $fieldType
	 * @return int
	 */
	public static function getDataTypeFromFieldType($fieldType) {
		$types = array(
			'captcha' => 0,
			'check' => 1,
			'content' => 0,
			'date' => 2,
			'file' => 3,
			'hidden' => 0,
			'html' => 0,
			'input' => 0,
			'location' => 0,
			'password' => 0,
			'radio' => 0,
			'reset' => 0,
			'select' => 1,
			'submit' => 0,
			'text' => 0,
			'textarea' => 0,
			'typoscript' => 0
		);

		// extend dataType with TSConfig
		$typoScriptConfiguration = BackendUtility::getPagesTSconfig($GLOBALS['TSFE']->id);
		$extensionConfiguration = $typoScriptConfiguration['tx_powermail.']['flexForm.'];
		if (!empty($extensionConfiguration['type.']['addFieldOptions.'][$fieldType . '.']['dataType'])) {
			$types[$fieldType] = intval($extensionConfiguration['type.']['addFieldOptions.'][$fieldType . '.']['dataType']);
		}

		if (array_key_exists($fieldType, $types)) {
			return $types[$fieldType];
		}
		return 0;
	}

	/**
	 * Overwrite a string if a TypoScript cObject is available
	 *
	 * @param string $string Value to overwrite
	 * @param array $conf TypoScript Configuration Array
	 * @param string $key Key for TypoScript Configuration
	 * @return void
	 */
	public function overwriteValueFromTypoScript(&$string = NULL, $conf, $key) {
		$cObj = $this->configurationManager->getContentObject();

		if ($cObj->cObjGetSingle($conf[$key], $conf[$key . '.'])) {
			$string = $cObj->cObjGetSingle($conf[$key], $conf[$key . '.']);
		}
	}

	/**
	 * Parse String with Fluid View
	 *
	 * @param string $string Any string
	 * @param array $variables Variables
	 * @return string Parsed string
	 */
	public function fluidParseString($string, $variables = array()) {
		$parseObject = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		$parseObject->setTemplateSource($string);
		$parseObject->assignMultiple($variables);
		return $parseObject->render();
	}

	/**
	 * Use htmlspecialchars on array (key and value) (any depth - recursive call)
	 *
	 * @param array $array Any array
	 * @return array Cleaned array
	 */
	public function htmlspecialcharsOnArray($array) {
		$newArray = array();
		foreach ((array)$array as $key => $value) {
			if (is_array($value)) {
				$newArray[htmlspecialchars($key)] = $this->htmlspecialcharsOnArray($value);
			} else {
				$newArray[htmlspecialchars($key)] = htmlspecialchars($value);
			}
		}
		unset($array);
		return $newArray;
	}

	/**
	 * Get all receiver emails in an array
	 *
	 * @param string $receiverString String with some emails
	 * @param int $feGroup fe_groups Uid
	 * @return array
	 */
	public function getReceiverEmails($receiverString, $feGroup) {
		$array = $this->getEmailsFromString($receiverString);
		if ($feGroup) {
			$array = array_merge($array, $this->getEmailsFromFeGroup($feGroup));
		}
		if (self::getDevelopmentContextEmail()) {
			$array = array(self::getDevelopmentContextEmail());
		}
		return $array;
	}

	/**
	 * Read E-Mails from String
	 *
	 * @param int $uid fe_groups Uid
	 * @return array Array with emails
	 */
	public function getEmailsFromFeGroup($uid) {
		$users = $this->userRepository->findByUsergroup($uid);
		$array = array();
		foreach ($users as $user) {
			if (GeneralUtility::validEmail($user->getEmail())) {
				$array[] = $user->getEmail();
			}
		}
		return $array;
	}

	/**
	 * Read E-Mails from String
	 *
	 * @param string $string Any given string from a textarea with some emails
	 * @return array Array with emails
	 */
	public function getEmailsFromString($string) {
		$array = array();
		$string = str_replace(array(
			"\n",
			'|',
			','
		), ';', $string);
		$arr = GeneralUtility::trimExplode(';', $string, TRUE);
		foreach ($arr as $email) {
			$array[] = $email;
		}
		return $array;
	}

	/**
	 * Parse TypoScript from path like lib.blabla
	 *
	 * @param $typoScriptObjectPath
	 * @return string
	 */
	public static function parseTypoScriptFromTypoScriptPath($typoScriptObjectPath) {
		if (empty($typoScriptObjectPath)) {
			return '';
		}
		$setup = $GLOBALS['TSFE']->tmpl->setup;
		$contentObject = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$pathSegments = GeneralUtility::trimExplode('.', $typoScriptObjectPath);
		$lastSegment = array_pop($pathSegments);
		foreach ($pathSegments as $segment) {
			$setup = $setup[$segment . '.'];
		}
		return $contentObject->cObjGetSingle($setup[$lastSegment], $setup[$lastSegment . '.']);
	}

	/**
	 * Create an options array (Needed for fieldsettings: select, radio, check)
	 *        option1 =>
	 *            label => Red Shoes
	 *            value => red
	 *            selected => 1
	 *
	 * @param string $string Options from the Textarea
	 * @param string $typoScriptObjectPath Path to TypoScript like lib.blabla
	 * @return array Options Array
	 */
	public static function optionArray($string, $typoScriptObjectPath) {
		if (empty($string)) {
			$string = self::parseTypoScriptFromTypoScriptPath($typoScriptObjectPath);
		}
		if (empty($string)) {
			$string = 'Error, no options to show';
		}
		$options = array();
		$string = str_replace('[\n]', "\n", $string);
		$settingsField = GeneralUtility::trimExplode("\n", $string, TRUE);
		foreach ($settingsField as $line) {
			$settings = GeneralUtility::trimExplode('|', $line, FALSE);
			$value = (isset($settings[1]) ? $settings[1] : $settings[0]);
			$options[] = array(
				'label' => $settings[0],
				'value' => $value,
				'selected' => isset($settings[2]) ? 1 : 0
			);
		}

		return $options;
	}

	/**
	 * Powermail SendPost - Send values via curl to a third party software
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param \array $conf TypoScript Configuration
	 * @return void
	 */
	public function sendPost($mail, $conf) {
		$contentObject = $this->configurationManager->getContentObject();

		// switch of if disabled
		$enable = $contentObject->cObjGetSingle(
			$conf['marketing.']['sendPost.']['_enable'],
			$conf['marketing.']['sendPost.']['_enable.']
		);
		if (!$enable) {
			return;
		}

		$contentObject->start(
			$this->getVariablesWithMarkersFromMail($mail)
		);
		$parameters = $contentObject->cObjGetSingle(
			$conf['marketing.']['sendPost.']['values'],
			$conf['marketing.']['sendPost.']['values.']
		);
		$curlSettings = array(
			'url' => $conf['marketing.']['sendPost.']['targetUrl'],
			'params' => $parameters
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curlSettings['url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlSettings['params']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($ch);
		curl_close($ch);

		// Debug Output
		if ($conf['marketing.']['sendPost.']['debug']) {
			GeneralUtility::devLog(
				'SendPost Values',
				'powermail',
				0,
				$curlSettings
			);
		}
	}

	/**
	 * Returns array with alphabetical letters
	 *
	 * @return array
	 */
	public static function getAbcArray() {
		$arr = array();
		for ($a = A; $a != AA; $a++) {
			$arr[] = $a;
		}
		return $arr;
	}

	/**
	 * Check if String is JSON Array
	 *
	 * @param string $string
	 * @return bool
	 */
	public static function isJsonArray($string) {
		return is_array(json_decode($string, TRUE));
	}

	/**
	 * Check if String/Array is filled
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function isNotEmpty($value) {
		// bool
		if (is_bool($value)) {
			return FALSE;
		}
		// string (default fields)
		if (!is_array($value)) {
			if (isset($value) && strlen($value)) {
				return TRUE;
			}
			// array (checkboxes)
		} else {
			foreach ($value as $subValue) {
				if (isset($value) && strlen($subValue)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Check if logged in user is allowed to make changes in Pi2
	 *
	 * @param array $settings $settings TypoScript and Flexform Settings
	 * @param int|\In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isAllowedToEdit($settings, $mail) {
		if (!is_a($mail, '\In2code\Powermail\Domain\Model\Mail')) {
			$mail = $this->mailRepository->findByUid(intval($mail));
		}
		if (!$GLOBALS['TSFE']->fe_user->user['uid'] || $mail === NULL) {
			return FALSE;
		}

		$usergroups = GeneralUtility::trimExplode(',', $GLOBALS['TSFE']->fe_user->user['usergroup'], TRUE);
		$usersSettings = GeneralUtility::trimExplode(',', $settings['edit']['feuser'], TRUE);
		$usergroupsSettings = GeneralUtility::trimExplode(',', $settings['edit']['fegroup'], TRUE);

		// replace "_owner" with uid of owner in array with users
		if ($mail->getFeuser() !== NULL && is_numeric(array_search('_owner', $usersSettings))) {
			$usersSettings[array_search('_owner', $usersSettings)] = $mail->getFeuser()->getUid();
		}

		// add owner groups to allowed groups (if "_owner")
		if (is_numeric(array_search('_owner', $usergroupsSettings))) {
			$usergroupsFromOwner = $this->getUserGroupsFromUser($mail->getFeuser());
			$usergroupsSettings = array_merge((array)$usergroupsSettings, (array)$usergroupsFromOwner);
		}

		// 1. check user
		if (in_array($GLOBALS['TSFE']->fe_user->user['uid'], $usersSettings)) {
			return TRUE;
		}

		// 2. check usergroup
		if (count(array_intersect($usergroups, $usergroupsSettings))) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Check if
	 *
	 * @return bool
	 */
	public static function isBackendAdmin() {
		if (isset($GLOBALS['BE_USER']->user)) {
			return $GLOBALS['BE_USER']->user['admin'] === 1;
		}
		return FALSE;
	}

	/**
	 * Return usergroups uid of a given fe_user
	 *
	 * @param string $uid FE_user UID
	 * @return array Usergroups
	 */
	protected function getUserGroupsFromUser($uid) {
		$groups = array();
		$select = 'fe_groups.uid';
		$from = 'fe_users, fe_groups, sys_refindex';
		$where = 'sys_refindex.tablename = "fe_users"';
		$where .= ' AND sys_refindex.ref_table = "fe_groups"';
		$where .= ' AND fe_users.uid = sys_refindex.recuid AND fe_groups.uid = sys_refindex.ref_uid';
		$where .= ' AND fe_users.uid = ' . intval($uid);
		$groupBy = '';
		$orderBy = '';
		$limit = 1000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res) {
			// One loop for every entry
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$groups[] = $row['uid'];
			}
		}

		return $groups;
	}

	/**
	 * Check if given Hash is the correct Optin Hash
	 *
	 * @param \string $hash
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return \string
	 */
	public static function checkOptinHash($hash, Mail $mail) {
		$newHash = self::createHash($mail->getUid() . $mail->getPid() . $mail->getForm()->getUid());
		if (!empty($hash) && $newHash === $hash) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Create Hash for Optin Mail
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return \string
	 */
	public static function createOptinHash(Mail $mail) {
		return self::createHash($mail->getUid() . $mail->getPid() . $mail->getForm()->getUid());
	}

	/**
	 * Create Hash from String and TYPO3 Encryption Key
	 *
	 * @param string $string Any String
	 * @return string Hashed String
	 */
	public static function createHash($string) {
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'])) {
			$hash = GeneralUtility::shortMD5($string . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
		} else {
			$hash = GeneralUtility::shortMD5($string);
		}
		return $hash;
	}

	/**
	 * Plain String output for given array
	 *
	 * @param array $array
	 * @return string
	 */
	public static function viewPlainArray($array) {
		$string = '';
		foreach ((array)$array as $key => $value) {
			$string .= $key . ': ' . $value . "\n";
		}
		return $string;
	}

	/**
	 * Store Marketing Information in Session
	 *        'refererDomain' => domain.org
	 *        'referer' => http://domain.org/xyz/test.html
	 *        'country' => Germany
	 *        'mobileDevice' => 1
	 *        'frontendLanguage' => 3
	 *        'browserLanguage' => en-us
	 *        'feUser' => userAbc
	 *        'pageFunnel' => array(2, 5, 1)
	 *
	 * @param \string $referer Referer
	 * @param \int $language Frontend Language Uid
	 * @param \int $pid Page Id
	 * @param \int $mobileDevice Is mobile device?
	 * @return void
	 */
	public static function storeMarketingInformation($referer = NULL, $language = 0, $pid = 0, $mobileDevice = 0) {
		$marketingInfo = self::getSessionValue('powermail_marketing');

		// initially create array with marketing info
		if (!is_array($marketingInfo)) {
			$marketingInfo = array(
				'refererDomain' => self::getDomainFromUri($referer),
				'referer' => $referer,
				'country' => self::getCountryFromIp(),
				'mobileDevice' => $mobileDevice,
				'frontendLanguage' => $language,
				'browserLanguage' => GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
				'pageFunnel' => array($pid)
			);
		} else {
			// add current pid to funnel
			$marketingInfo['pageFunnel'][] = $pid;

			// clean pagefunnel if has more than 256 entries
			if (count($marketingInfo['pageFunnel']) > 256) {
				$marketingInfo['pageFunnel'] = array($pid);
			}
		}

		// store in session
		self::setSessionValue('powermail_marketing', $marketingInfo, TRUE);
	}

	/**
	 * Read MarketingInfos from Session
	 *
	 * @return array
	 */
	public static function getMarketingInfos() {
		$marketingInfo = self::getSessionValue('powermail_marketing');
		if (!is_array($marketingInfo)) {
			$marketingInfo = array(
				'refererDomain' => '',
				'referer' => '',
				'country' => '',
				'mobileDevice' => 0,
				'frontendLanguage' => 0,
				'browserLanguage' => '',
				'pageFunnel' => array()
			);
		}
		return $marketingInfo;
	}

	/**
	 * Get Property from currently logged in fe_user
	 *
	 * @param \string $propertyName
	 * @return \string
	 */
	public static function getPropertyFromLoggedInFeUser($propertyName = 'uid') {
		if (!empty($GLOBALS['TSFE']->fe_user->user[$propertyName])) {
			return $GLOBALS['TSFE']->fe_user->user[$propertyName];
		}
		return '';
	}

	/**
	 * Read domain from uri
	 *
	 * @param \string $uri
	 * @return \string
	 */
	public static function getDomainFromUri($uri) {
		$uriParts = parse_url($uri);
		return $uriParts['host'];
	}

	/**
	 * Get Country Name out of an IP address
	 *
	 * @param string $ip
	 * @return string Countryname
	 */
	public static function getCountryFromIp($ip = NULL) {
		if ($ip === NULL) {
			$ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
		}
		$json = GeneralUtility::getUrl('http://www.telize.com/geoip/' . $ip);
		if ($json) {
			$geoInfo = json_decode($json);
			if (!empty($geoInfo->country)) {
				return $geoInfo->country;
			}
		}
		return '';
	}

	/**
	 * Set a powermail session (don't overwrite existing sessions)
	 *
	 * @param string $name A session name
	 * @param array $values Values to save
	 * @param \bool $overwrite Overwrite existing values
	 * @return void
	 */
	public static function setSessionValue($name, $values, $overwrite = FALSE) {
		if (!$overwrite) {
			// read existing values
			$oldValues = self::getSessionValue($name);
			// merge old values with new
			$values = array_merge((array)$oldValues, (array)$values);
		}
		$newValues = array(
			$name => $values
		);

		$GLOBALS['TSFE']->fe_user->setKey('ses', self::$extKey, $newValues);
		$GLOBALS['TSFE']->storeSessionData();
	}

	/**
	 * Read a powermail session
	 *
	 * @param \string $name A session name
	 * @return \string Values from session
	 */
	public static function getSessionValue($name = '') {
		$powermailSession = $GLOBALS['TSFE']->fe_user->getKey('ses', self::$extKey);
		if (!empty($name) && isset($powermailSession[$name])) {
			return $powermailSession[$name];
		}
		return '';
	}

	/**
	 * Save values to any table in TYPO3 database
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param array $conf TypoScript Configuration
	 * @return void
	 */
	public function saveToAnyTable($mail, $conf) {
		if (empty($conf['dbEntry.'])) {
			return;
		}
		$contentObject = $this->configurationManager->getContentObject();
		$startArray = $this->getVariablesWithMarkersFromMail($mail);

		// one loop per table
		foreach ((array) array_keys($conf['dbEntry.']) as $table) {
			$contentObject->start($startArray);

			// remove ending .
			$table = substr($table, 0, -1);

			// skip this table if disabled
			$enable = $contentObject->cObjGetSingle(
				$conf['dbEntry.'][$table . '.']['_enable'],
				$conf['dbEntry.'][$table . '.']['_enable.']
			);
			if (!$enable) {
				continue;
			}

			/* @var $saveToAnyTable \In2code\Powermail\Utility\SaveToAnyTable */
			$saveToAnyTable = $this->objectManager->get('In2code\Powermail\Utility\SaveToAnyTable', $table);
			if (!empty($conf['dbEntry.'][$table . '.']['_ifUnique.'])) {
				$uniqueFields = array_keys($conf['dbEntry.'][$table . '.']['_ifUnique.']);
				$saveToAnyTable->setMode($conf['dbEntry.'][$table . '.']['_ifUnique.'][$uniqueFields[0]]);
				$saveToAnyTable->setUniqueField($uniqueFields[0]);
			}

			// one loop per field
			foreach ((array) $conf['dbEntry.'][$table . '.'] as $field => $settingsInner) {
				$settingsInner = NULL;

				// skip if key. or if it starts with _
				if (stristr($field, '.') || $field[0] === '_') {
					continue;
				}

				// read from TypoScript
				$value = $contentObject->cObjGetSingle(
					$conf['dbEntry.'][$table . '.'][$field],
					$conf['dbEntry.'][$table . '.'][$field . '.']
				);
				$saveToAnyTable->addProperty($field, $value);
			}
			if (!empty($conf['debug.']['saveToTable'])) {
				$saveToAnyTable->setDevLog(TRUE);
			}
			$uid = $saveToAnyTable->execute();

			// add this uid to startArray for later using in TypoScript
			$startArray = array_merge(
				$startArray,
				array('uid_' . $table => $uid)
			);
		}
	}

	/**
	 * Read pid from current URL
	 *        URL example:
	 *        http://powermailt361.in2code.de/typo3/alt_doc.php?&
	 *        returnUrl=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D17%23
	 *        element-tt_content-14&edit[tt_content][14]=edit
	 *
	 * @return int
	 */
	public static function getPidFromBackendPage() {
		$pid = 0;
		$backUrl = str_replace('?', '&', GeneralUtility::_GP('returnUrl'));
		$urlParts = GeneralUtility::trimExplode('&', $backUrl, TRUE);
		foreach ($urlParts as $part) {
			if (stristr($part, 'id=')) {
				$pid = str_replace('id=', '', $part);
			}
		}

		return intval($pid);
	}

	/**
	 * Get Subfolder of current TYPO3 Installation
	 *        and never return "//"
	 *
	 * @param bool $leadingSlash will be prepended
	 * @param bool $trailingSlash will be appended
	 * @param string $testHost can be used for a test
	 * @param string $testUrl can be used for a test
	 * @return string
	 */
	public static function getSubFolderOfCurrentUrl($leadingSlash = TRUE, $trailingSlash = TRUE, $testHost = NULL, $testUrl = NULL) {
		$subfolder = '';
		$typo3RequestHost = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
		if ($testHost) {
			$typo3RequestHost = $testHost;
		}
		$typo3SiteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
		if ($testUrl) {
			$typo3SiteUrl = $testUrl;
		}

		// if subfolder
		if ($typo3RequestHost . '/' !== $typo3SiteUrl) {
			$subfolder = substr(str_replace($typo3RequestHost . '/', '', $typo3SiteUrl), 0, -1);
		}
		if ($trailingSlash && substr($subfolder, 0, -1) !== '/') {
			$subfolder .= '/';
		}
		if ($leadingSlash && $subfolder[0] !== '/') {
			$subfolder = '/' . $subfolder;
		}
		return $subfolder;
	}

	/**
	 * createRandomFileName
	 *
	 * @param int $length
	 * @param bool $lowerAndUpperCase
	 * @return string
	 */
	public static function createRandomString($length = 32, $lowerAndUpperCase = TRUE) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		if ($lowerAndUpperCase) {
			$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$fileName = '';
		for ($i = 0; $i < $length; $i++) {
			$key = mt_rand(0, strlen($characters) - 1);
			$fileName .= $characters[$key];
		}
		return $fileName;
	}

	/**
	 * Get image src from any string with an image tag
	 *
	 * @param string $html
	 * @return mixed
	 */
	public static function getImageSourceFromTag($html) {
		preg_match('~<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>~i', $html, $matches);
		return $matches[1];
	}

	/**
	 * Simple function that returns fallback variable
	 * if main variable is empty to save unnecessary
	 * long if statements
	 *
	 * @param mixed $variable
	 * @param mixed $fallback
	 * @return mixed
	 */
	public static function conditionalVariable($variable, $fallback) {
		if (empty($variable)) {
			return $fallback;
		}
		return $variable;
	}

	/**
	 * Return configured captcha extension
	 *
	 * @param array $settings
	 * @return string
	 */
	public static function getCaptchaExtensionFromSettings($settings) {
		$allowedExtensions = array(
			'captcha'
		);
		if (
			in_array($settings['captcha.']['use'], $allowedExtensions) &&
			ExtensionManagementUtility::isLoaded($settings['captcha.']['use'])
		) {
			return $settings['captcha.']['use'];
		}
		return 'default';
	}

	/**
	 * Add parameters to piVars from TypoScript
	 *
	 * @param array $pluginVariables
	 * @param array $parameters
	 * @return void
	 */
	public static function prepareFilterPluginVariables(&$pluginVariables, $parameters) {
		if (!empty($parameters['filter'])) {
			$pluginVariables = (array) $pluginVariables + (array) $parameters;
		}
	}

	/**
	 * Send a plain mail for simple notifies
	 *
	 * @param string $receiverEmail Email address to send to
	 * @param string $senderEmail Email address from sender
	 * @param string $subject Subject line
	 * @param string $body Message content
	 * @return bool mail was sent?
	 */
	public static function sendPlainMail($receiverEmail, $senderEmail, $subject, $body) {
		/** @var \TYPO3\CMS\Core\Mail\MailMessage $message */
		$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$message->setTo(array($receiverEmail => ''));
		$message->setFrom(array($senderEmail => 'Sender'));
		$message->setSubject($subject);
		$message->setBody($body);
		$message->send();
		return $message->isSent();
	}

	/**
	 * Get development email (only if in dev context)
	 *
	 * @return false|string
	 */
	public static function getDevelopmentContextEmail() {
		if (
			GeneralUtility::getApplicationContext()->isDevelopment() &&
			GeneralUtility::validEmail($GLOBALS['TYPO3_CONF_VARS']['EXT']['powermailDevelopContextEmail'])
		) {
			return $GLOBALS['TYPO3_CONF_VARS']['EXT']['powermailDevelopContextEmail'];
		}
		return FALSE;
	}

	/**
	 * Get Fieldlist from Form UID
	 *
	 * @param int $formUid Form UID
	 * @return array
	 */
	public static function getFieldsFromFormWithSelectQuery($formUid) {
		$select = '
			tx_powermail_domain_model_fields.uid,
			tx_powermail_domain_model_fields.title,
			tx_powermail_domain_model_fields.sender_email,
			tx_powermail_domain_model_fields.sender_name
		';
		$select .= ', tx_powermail_domain_model_fields.marker';
		$from = '
			tx_powermail_domain_model_fields
			left join tx_powermail_domain_model_pages on tx_powermail_domain_model_fields.pages = tx_powermail_domain_model_pages.uid
			left join tx_powermail_domain_model_forms on tx_powermail_domain_model_pages.forms = tx_powermail_domain_model_forms.uid
		';
		$where = '
			tx_powermail_domain_model_fields.deleted = 0 and
			tx_powermail_domain_model_fields.hidden = 0 and
			tx_powermail_domain_model_fields.type != "submit" and
			tx_powermail_domain_model_fields.sys_language_uid IN (-1,0) and
			tx_powermail_domain_model_forms.uid = ' . intval($formUid);
		$groupBy = '';
		$orderBy = 'tx_powermail_domain_model_fields.sorting ASC';
		$limit = 10000;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

		$array = array();
		if ($res) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$array[] = $row;
			}
		}

		return $array;
	}

	/**
	 * Merges Flexform, TypoScript and Extension Manager Settings (up to 2 levels)
	 *        Note: It's not possible to have the same field in TS and Flexform
	 *        and if FF value is empty, we want the TypoScript value instead
	 *
	 * @param array $settings All settings
	 * @param string $typoScriptLevel Startpoint
	 * @return void
	 */
	public static function mergeTypoScript2FlexForm(&$settings, $typoScriptLevel = 'setup') {
		// config
		$temporarySettings = array();
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);

		if (isset($settings[$typoScriptLevel]) && is_array($settings[$typoScriptLevel])) {
			// copy typoscript part to conf part
			$temporarySettings = $settings[$typoScriptLevel];
		}

		if (isset($settings['flexform']) && is_array($settings['flexform'])) {
			// copy flexform part to conf part
			$temporarySettings = array_merge((array)$temporarySettings, (array)$settings['flexform']);
		}

		// merge ts and ff (loop every flexform)
		foreach ($temporarySettings as $key1 => $value1) {
			// 1. level
			if (!is_array($value1)) {
				// only if this key exists in ff and ts
				if (isset($settings[$typoScriptLevel][$key1]) && isset($settings['flexform'][$key1])) {
					// only if ff is empty and ts not
					if ($settings[$typoScriptLevel][$key1] && !$settings['flexform'][$key1]) {
						// overwrite with typoscript settings
						$temporarySettings[$key1] = $settings[$typoScriptLevel][$key1];
					}
				}
			} else {
				// 2. level
				foreach ($value1 as $key2 => $value2) {
					$value2 = NULL;

					// only if this key exists in ff and ts
					if (isset($settings[$typoScriptLevel][$key1][$key2]) && isset($settings['flexform'][$key1][$key2])) {
						// only if ff is empty and ts not
						if ($settings[$typoScriptLevel][$key1][$key2] && !$settings['flexform'][$key1][$key2]) {
							// overwrite with typoscript settings
							$temporarySettings[$key1][$key2] = $settings[$typoScriptLevel][$key1][$key2];
						}
					}
				}
			}
		}

		// merge ts and ff (loop every typoscript)
		foreach ((array)$settings[$typoScriptLevel] as $key1 => $value1) {
			// 1. level
			if (!is_array($value1)) {
				// only if this key exists in ts and not in ff
				if (isset($settings[$typoScriptLevel][$key1]) && !isset($settings['flexform'][$key1])) {
					// set value from ts
					$temporarySettings[$key1] = $value1;
				}
			} else {
				// 2. level
				foreach ($value1 as $key2 => $value2) {
					// only if this key exists in ts and not in ff
					if (isset($settings[$typoScriptLevel][$key1][$key2]) && !isset($settings['flexform'][$key1][$key2])) {
						// set value from ts
						$temporarySettings[$key1][$key2] = $value2;
					}
				}
			}
		}

		// add global config
		$temporarySettings['global'] = $confArr;

		$settings = $temporarySettings;
		unset($temporarySettings);
	}
}