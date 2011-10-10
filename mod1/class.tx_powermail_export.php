<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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


require_once(t3lib_extMgm::extPath('powermail') . 'lib/class.tx_powermail_functions_div.php');

if (t3lib_extMgm::isLoaded('phpexcel_library')) {
	require_once(t3lib_extMgm::extPath('phpexcel_library') . 'sv1/class.tx_phpexcellibrary_sv1.php');
	$PHPExcelSV = t3lib_div::makeInstance('tx_phpexcellibrary_sv1');
	$PHPExcelSV->init();
}


/**
 * Plugin 'tx_powermail_export' for the 'powermail' extension.
 *
 * @author	powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
 * @package	TYPO3
 * @subpackage tx_powermail
 */
class tx_powermail_export {

	/**
	 * Extension key
	 *
	 * @var	string
	 */
	var $extKey = 'powermail';

	/**
	 * Pid
	 *
	 * @var	string
	 */
	var $pid;

	/**
	 * Export method
	 *
	 * @var	string
	 */
	var $export;

	/**
	 * Debug
	 *
	 * @var	boolean
	 */
	var $debug = false;

	/**
	 * dateFormat for date values
	 *
	 * @var	string
	 */
	var $dateFormat = 'Y-m-d';

	/**
	 * timeFormat for time values
	 *
	 * @var	string
	 */
	var $timeFormat = 'H:i:s';

	/**
	 * dateTimeFormat for datetime values
	 *
	 * @var	string
	 */
	var $datetimeFormat = 'Y-m-d H:i';

	/**
	 * Time filter prefix for export file
	 *
	 * @var	string
	 */
	var $timeFilePrefix = '';

	/**
	 * filename of export file
	 *
	 * @var	string
	 */
	var $filename = 'powermail';

	/**
	 * Prefix for export files
	 *
	 * @var	string
	 */
	var $filePrefix = 'powermail';

	/**
	 * Suffix for EXCEL export file
	 *
	 * @var	string
	 */
	var $xlsFileSuffix = '.xlsx';

	/**
	 * Fileformat for EXCEL export file
	 *
	 * @var	string
	 */
	var $xlsFileFormat = 'Excel2007';

	/**
	 * Autosize columns of EXCEL export file
	 *
	 * @var	boolean
	 */
	var $xlsAutoSize = true;

	/**
	 * Suffix for CSV export file
	 *
	 * @var	string
	 */
	var $csvFileSuffix = '.csv';

	/**
	 * Default encoding for CSV export file
	 *
	 * @var	string
	 */
	var $csvDefaultEncoding = 'iso-8859-15';

	/**
	 * Separator for CSV export file
	 *
	 * @var	string
	 */
	var $seperator = ';';

	/**
	 * Suffix for HTML export file
	 *
	 * @var	string
	 */
	var $htmlFileSuffix = '.html';

	/**
	 * Activate CSV file compressing to .gz
	 *
	 * @var	bool
	 */
	var $zip = false;

	/**
	 * $LANG object
	 *
	 * @var	language
	 */
	var $LANG = null;

	/**
	 * Encoding for data output
	 *
	 * @var	string
	 */
	var $outputEncoding;

	/**
	 * Module's tsConfig
	 *
	 * @var	array
	 */
	var $tsConfig;

	/**
	 * Order for export
	 *
	 * @var	array
	 */
	var $rowConfig = array(
		'number' => '#',
		'date' => 'Date',
		'time' => 'Time',
		'uid' => 'all',
		'sender' => 'Sender email',
		'senderIP' => 'Sender IP address',
		'recipient' => 'Recipient email',
		'subject_r' => 'Email subject',
		'formid' => 'Page ID',
		'UserAgent' => 'UserAgent',
		'Referer' => 'Referer',
		'SP_TZ' => 'Sender location'
	);

	/**
	 * startDate
	 *
	 * @var	string
	 */
	var $defaultStart;

	/**
	 * endDate
	 *
	 * @var	string
	 */
	var $defaultEnd;

	/**
	 * overwriteFilename
	 *
	 * @var	string
	 */
	var $overwriteFilename;

	/**
	 * number of results
	 *
	 * @var int
	 */
	var $resNumRows;

	/**
	 * Dispatcher main method for export
	 *
	 * @return	string
	 */
	public function main() {
		$this->debug = t3lib_extMgm::isLoaded('devlog');

		$this->pid = intval($this->pid);

		$pageArray = t3lib_BEfunc::getRecord('pages', $this->pid, 'title');
		$this->pageTitle = $pageArray['title'];

		$this->phpexcel = t3lib_extMgm::isLoaded('phpexcel_library');
		$this->header = '';
		$this->content = '';
		$i = 0;


		// Set absolute path to typo3temp dir
		$this->absFilePath = PATH_site . 'typo3temp/';

		$this->timeFilter = '';
		if ($this->startDateTime > 0) {
			$this->timeFilter .= ' AND crdate > ' . intval($this->startDateTime);
			$this->timeFilePrefix .= strftime('_%Y-%m-%d_%H.%M', intval($this->startDateTime));
		}
		if ($this->endDateTime > 0) {
			$this->timeFilter .= ' AND crdate < ' . intval($this->endDateTime);
			$this->timeFilePrefix .= strftime('_%Y-%m-%d_%H.%M', intval($this->endDateTime));
		}


		// Get values from page tsConfig
		$this->tsConfig = t3lib_BEfunc::getModtsConfig($this->pid, 'tx_powermail_mod1');

		$this->useTitle = true;
		if (isset($this->tsConfig['properties']['config.']['export.']['useTitle'])) {
			$this->useTitle = ($this->tsConfig['properties']['config.']['export.']['useTitle'] == '0') ? false : true;
		}

		$this->exportHeaderLanguageUid = 0;
		if (isset($this->tsConfig['properties']['config.']['export.']['allTitleLanguageUid'])) {
			$this->exportHeaderLanguageUid = intval($this->tsConfig['properties']['config.']['export.']['allTitleLanguageUid']);
		}

		if (isset($this->tsConfig['properties']['config.']['export.']['dateFormat'])) {
			$this->dateFormat = $this->tsConfig['properties']['config.']['export.']['dateFormat'];
		}

		if (isset($this->tsConfig['properties']['config.']['export.']['timeFormat'])) {
			$this->timeFormat = $this->tsConfig['properties']['config.']['export.']['timeFormat'];
		}

		if (isset($this->tsConfig['properties']['config.']['export.']['datetimeFormat'])) {
			$this->datetimeFormat = $this->tsConfig['properties']['config.']['export.']['datetimeFormat'];
		}

		// Not used Yet!
		$this->excludeFromAll = array();
		if (isset($this->tsConfig['properties']['config.']['export.']['excludeFromAll'])) {
			$this->excludeFromAll = explode(',', $this->tsConfig['properties']['config.']['export.']['excludeFromAll']);
		}

		if (isset($this->tsConfig['properties']['config.']['export.']['xls.']['format'])) {
			$this->xlsFileFormat = $this->tsConfig['properties']['config.']['export.']['xls.']['format'];
		}

		if ($this->xlsFileFormat != 'Excel2007') {
			$this->xlsFileSuffix = '.xls';
		}

		if (isset($this->tsConfig['properties']['config.']['export.']['xls.']['autoSize'])) {
			$this->xlsAutoSize = ($this->tsConfig['properties']['config.']['export.']['xls.']['autoSize'] == '1') ? true
					: false;
		}

		if (count($this->tsConfig['properties']['export.']) > 0) {
			$this->rowConfig = $this->tsConfig['properties']['export.'];
		}

		$this->setDateTimeFormat();
		$this->setEncoding();
		$this->setFilenames();
		$this->generateFormtypesArray();

		$this->hook_exportclass(); // adds hook

		$this->generalRecordsFilter = ' AND hidden = 0 AND deleted = 0';
		$select = '*';
		$from = 'tx_powermail_mails';
		$where = 'pid = ' . $this->pid . $this->timeFilter . $this->generalRecordsFilter;
		$groupBy = $limit = '';
		$orderBy = 'crdate DESC';
		$this->res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$this->resNumRows = $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);

		// If on current page is a result
		if ($this->res) {
			switch ($this->export) {
				case 'xls':
					$this->generateXlsTable();
					$this->generateFileHeader();
					break;
				case 'csv':
					$this->generateCsvTable();
					$this->generateFileHeader();
					break;
				case 'html':
					$this->generateHtmlTable();
					break;
				case 'email_xls':
					$this->generateXlsTable();
					$this->writeContentToTypo3tempDir();
					break;
				case 'email_csv':
					$this->generateCsvTable();
					$this->writeContentToTypo3tempDir();
					break;
				case 'email_html':
					$this->generateHtmlTable();
					$this->writeContentToTypo3tempDir();
					break;
				default:
					die ('no export method given!');
					break;
			}
		}

		// Delete all exported mails now
		if (t3lib_div::_GET('delafterexport') == 1) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_powermail_mails',
				'pid = ' . $this->pid . $this->generalRecordsFilter,
				array(
					 'deleted' => 1
				)
			);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($this->res);

		return $this->header . $this->content;
	}

	/**
	 * Generate HTML Table and stores result in $this->content
	 *
	 * @return	void
	 */
	protected function generateHtmlTable() {

		$htmlHeader = '<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head><title>Powermail HTML Export</title>
<meta http-equiv="Content-Type" content="text/html; charset=' . $this->outputEncoding . '" />
<style type="text/css">
table{font-size:10px;font-family:Arial,Helvetica,_sans_serif;}
th{background:#999;color:#fff;padding:2px;}
td{background:#fff;color:#333;padding:2px;}
tr.odd td{background:#eee;}
</style></head>
<body>';
		$htmlFooter = '</body></html>';


		$htmlContent = '<table>';

		// Generate table header
		$tableHeaderContent = '';
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, $this->getRowWithMostPiVars());
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res);
		$headerPiVars = t3lib_div::xml2array($row['piVars'], 'piVars');
		// Get form type of piVars

		$tableHeaderContent .= '<thead><tr>';
		foreach ($this->rowConfig as $key => $value) {
			$value = $this->charConvert(trim($value));
			// Static values
			if ($key != 'uid') {
				$tableHeaderContent .= '<th>' . htmlspecialchars($value) . '</th>';
			} else {
				if (isset($headerPiVars) && is_array($headerPiVars)) {
					foreach ($headerPiVars as $key => $value) {
						$this->fieldUid = $key;
						$this->getFieldLabelFromBackend();
						$label = $this->charConvert($this->fieldLabel);
						$tableHeaderContent .= '<th>' . $label . '</th>';
					}
				}
			}
		}
		$tableHeaderContent .= '</tr></thead>';

		if ($this->useTitle) {
			$htmlContent .= $tableHeaderContent;
		}

		// Generate table body
		$htmlContent .= '<tbody>';
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, 0);
		while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))) {
			$uploadURLPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $row['uploadPath'];
			if ($row['piVars']) {
				$piVars = t3lib_div::xml2array($row['piVars'], 'piVars');
				$i++;
				$htmlContent .= ($i % 2 === 0) ? '<tr>' : '<tr class="odd">';
				foreach ($this->rowConfig as $key => $value) {
					// If current row is number
					if ($key == 'number') {
						$htmlContent .= '<td>' . $i . '.</td>';

						// If current row is date
					} elseif ($key == 'date') {
						$htmlContent .= '<td>' . date($this->dateFormat, $row['crdate']) . '</td>';

						// If current row is time
					} elseif ($key == 'time') {
						$htmlContent .= '<td>' . date($this->timeFormat, $row['crdate']) . '</td>';

						// If current row should show all dynamic values (piVars)
					} elseif ($key == 'uid') {
						if (isset($piVars) && is_array($piVars)) {

							// One loop for every piVar
							foreach ($piVars as $key => $value) {
								if (!is_array($value)) {
									$value = $this->charConvert($this->cleanString($value));
									switch ($this->formtypes[$key]) {
										case 'date':
											$value = ($value == intval($value)) ? gmdate($this->dateFormat, $value)
													: $value;
											break;
										case 'datetime':
											$value = ($value == intval($value)) ? gmdate($this->datetimeFormat, $value)
													: $value;
											break;
										case 'file':
											$value = '<a href="' . $uploadURLPath . $value . '">' . $value . '</a>';
											break;
									}
									$htmlContent .= '<td>' . $value . '</td>';
								} else {
									$htmlContentSecondLevel = array();
									// One loop for every piVar in second level
									foreach ($piVars[$key] as $key2 => $value2) {
										if ($value2 != '') {
											$htmlContentSecondLevel[] .= $this->charConvert($this->cleanString($value2));
										}
									}
									$htmlContent .= '<td>' . implode(', ', $htmlContentSecondLevel) . '</td>';
								}
							}
							$piVarsCounter = count($piVars);
							while ($piVarsCounter < count($headerPiVars)) {
								$htmlContent .= '<td></td>';
								$piVarsCounter++;
							}
						}

						// Dynamic value like uid45
					} elseif (is_numeric(str_replace(array('uid', '_'), '', $key))) {

						// Explode uid44_0 to uid44 and 0
						$newkey = explode('_', $key);
						// piVars in first level
						//if($this->debug) t3lib_div::devLog('$key: ' . $key, $this->extKey, 0, $piVars);
						$orgkey = $this->getOriginalLanguageFieldUid($piVars, $key);
						//if($this->debug) t3lib_div::devLog('$orgkey: ' . $orgkey, $this->extKey, 0, $piVars);
						if (!is_array($piVars[$key]) && !is_array($piVars[$orgkey])) {

							$piVars[$key] = trim($piVars[$key]);
							// If $piVars[$key] is empty lookup for $key from original language
							if ($piVars[$key] == '') {
								$key = $this->getOriginalLanguageFieldUid($piVars, $key);
							}
							//$htmlContent .= '<td>' . $this->charConvert($this->cleanString($piVars[$key])) . '</td>';

							$value = $this->charConvert($this->cleanString($piVars[$key]));
							switch ($this->formtypes[$key]) {
								case 'date':
									$value = ($value == intval($value)) ? gmdate($this->dateFormat, $value) : $value;
									break;
								case 'datetime':
									$value = ($value == intval($value)) ? gmdate($this->datetimeFormat, $value)
											: $value;
									break;
								case 'file':
									$value = '<a href="' . $uploadURLPath . $value . '">' . $value . '</a>';
									break;
							}
							$htmlContent .= '<td>' . $value . '</td>';

							// PiVars in second level
						} else {
							if ($orgkey != $key) {
								$newkey = explode('_', $orgkey);
							}
							$htmlContentSecondLevel = array();
							foreach ($piVars[$newkey[0]] as $key2 => $value2) {
								if ($value2 != '') {
									$htmlContentSecondLevel[] .= $this->charConvert($this->cleanString($value2));
								}
							}
							$htmlContent .= '<td>' . $this->charConvert($this->cleanString(implode(', ', $htmlContentSecondLevel))) . '</td>';
						}
					} else {
						$htmlContent .= '<td>' . $this->charConvert($row[$key]) . '</td>';
					}
				}
				$htmlContent .= '</tr>';
			}
		}
		$htmlContent .= '</tbody></table>';
		$this->content .= $htmlHeader . $htmlContent . $htmlFooter;
	}

	/**
	 * Generate CSV Table and stores result in $this->content
	 *
	 * @return	void
	 */
	protected function generateCsvTable() {
		$csvContent = '';
		$csvHeader = '';

		// Generate CSV Header
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, $this->getRowWithMostPiVars());
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res);
		$headerPiVars = t3lib_div::xml2array(($row['piVars']), 'piVars');
		foreach ($this->rowConfig as $key => $value) {
			$newValue = $this->charConvert($value);
			if (trim($newValue != '')) {
				$value = $newValue;
			}
			// Static values
			if ($key != 'uid') {
				$csvHeader .= '"' . $value . '"' . $this->seperator;
			} else {
				if (isset($headerPiVars) && is_array($headerPiVars)) {
					foreach ($headerPiVars as $key => $value) {
						$this->fieldUid = $key;
						$this->getFieldLabelFromBackend();
						$label = $this->charConvert($this->fieldLabel);
						$csvHeader .= '"' . $this->cleanString($label) . '"' . $this->seperator;
					}
				}
			}
		}
		$csvHeader = substr($csvHeader, 0, -1) . "\n";

		if ($this->useTitle) {
			$csvContent .= $csvHeader;
		}

		// Generate CSV Rows
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, 0);
		$i = 0;
		while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))) {
			$uploadURLPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $row['uploadPath'];
			if ($row['piVars']) {
				$i++;
				$piVars = t3lib_div::xml2array(($row['piVars']), 'piVars');
				//if($this->debug) t3lib_div::devLog('piVars found: ' . $i, $this->extKey, 0, $piVars);

				foreach ($this->rowConfig as $key => $value) {
					// If current row is number
					if ($key == 'number') {
						$csvContent .= '"' . $i . '."' . $this->seperator;

						// If current row is date
					} elseif ($key == 'date') {
						$csvContent .= '"' . date($this->dateFormat, $row['crdate']) . '"' . $this->seperator;

						// If current row is time
					} elseif ($key == 'time') {
						$csvContent .= '"' . date($this->timeFormat, $row['crdate']) . '"' . $this->seperator;

						// If current row should show all dynamic values (piVars)
					} elseif ($key == 'uid') {
						if (isset($piVars) && is_array($piVars)) {
							// One loop for every piVar
							foreach ($piVars as $key => $value) {
								if (!is_array($value)) {
									$value = $this->charConvert($this->cleanString($value));
									switch ($this->formtypes[$key]) {
										case 'date':
											$value = ($value == intval($value)) ? gmdate($this->dateFormat, $value)
													: $value;
											break;
										case 'datetime':
											$value = ($value == intval($value)) ? gmdate($this->datetimeFormat, $value)
													: $value;
											break;
										case 'file':
											$value = $uploadURLPath . $value;
											break;
									}
									$csvContent .= '"' . $value . '"' . $this->seperator;
								} else {

									$csvContentSecondLevel = array();
									// One loop for every piVar in second level
									foreach ($piVars[$key] as $value2) {
										if ($value2 != '') {
											$csvContentSecondLevel[] .= $this->charConvert($this->cleanString($value2));
										}
									}
									$csvContent .= '"' . implode(', ', $csvContentSecondLevel) . '"' . $this->seperator;
								}
							}
							$piVarsCounter = count($piVars);
							while ($piVarsCounter < count($headerPiVars)) {
								$csvContent .= $this->seperator;
								$piVarsCounter++;
							}
						}

						// Dynamic value like uid45
					} elseif (is_numeric(str_replace(array('uid', '_'), '', $key))) {

						// Explode uid44_0 to uid44 and 0
						$newkey = explode('_', $key);
						// piVars in first level
						//if($this->debug) t3lib_div::devLog('$key: ' . $key, $this->extKey, 0, $piVars);
						$orgkey = $this->getOriginalLanguageFieldUid($piVars, $key);
						//if($this->debug) t3lib_div::devLog('$orgkey: ' . $orgkey, $this->extKey, 0, $piVars);
						if (!is_array($piVars[$key]) && !is_array($piVars[$orgkey])) {

							$piVars[$key] = trim($piVars[$key]);
							// If $piVars[$key] is empty lookup for $key from original language
							if ($piVars[$key] == '') {
								$key = $this->getOriginalLanguageFieldUid($piVars, $key);
							}
							//$csvContent .= '"' . $this->charConvert($this->cleanString($piVars[$key])) . '"' . $this->seperator;

							$value = $this->charConvert($this->cleanString($piVars[$key]));
							switch ($this->formtypes[$key]) {
								case 'date':
									$value = ($value == intval($value)) ? gmdate($this->dateFormat, $value) : $value;
									break;
								case 'datetime':
									$value = ($value == intval($value)) ? gmdate($this->datetimeFormat, $value)
											: $value;
									break;
								case 'file':
									$value = $uploadURLPath . $value;
									break;
							}
							$csvContent .= '"' . $value . '"' . $this->seperator;

							// PiVars in second level
						} else {
							if ($orgkey != $key) {
								$newkey = explode('_', $orgkey);
							}
							$csvContentSecondLevel = array();
							foreach ($piVars[$newkey[0]] as $key2 => $value2) {
								if ($value2 != '') {
									$csvContentSecondLevel[] .= $this->charConvert($this->cleanString($value2));
								}
							}
							$csvContent .= '"' . implode(', ', $csvContentSecondLevel) . '"' . $this->seperator;
						}

					} else {
						$csvContent .= '"' . $this->charConvert($row[$key]) . '"' . $this->seperator;
					}
				}

				// Delete last seperator
				$csvContent = substr($csvContent, 0, -1);
				$csvContent .= "\n";
			}
		}
		$this->content .= $csvContent;
		//if($this->debug) t3lib_div::devLog($this->content, $this->extKey, 0);
	}

	/**
	 * Generate EXCEL Table and stores result in $this->content
	 *
	 * @return	void
	 */
	protected function generateXlsTable() {
		if ($this->phpexcel) {

			$sheetRow = 1;
			$excelObject = new PHPExcel();

			// Set properties
			$excelObject->getProperties()->setCreator('Powermail');
			$excelObject->getProperties()->setTitle('Powermail Export');
			$excelObject->getProperties()->setDescription('This document was exported from the TYPO3 extension "powermail".');
			/*
			$excelObject->getProperties()->setLastModifiedBy("Maarten Balliauw");
			$excelObject->getProperties()->setSubject("Office 2007 XLSX Test Document");
			*/
			// Rename sheet
			$title = $this->LANG->getLL('title');
			if (empty($title)) {
				$title = 'powermail export';
			} else {
				$title = substr($title, 0, 31);
			}
			$excelObject->getActiveSheet()->setTitle($title);
			$excelObject->setActiveSheetIndex(0);

			// Generate EXCEL Header
			if ($this->useTitle) {
				$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, $this->getRowWithMostPiVars());
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res);
				$headerPiVars = t3lib_div::xml2array($row['piVars'], 'piVars');
				$sheetHeaderCol = 0;
				$sheetHeaderCols = array();
				$excelColNames = $this->getExcelColNames(intval(count($headerPiVars) + count($this->rowConfig)));
				if ($this->debug) {
					t3lib_div::devLog('Generated excelColNames: ', $this->extKey, 0, $excelColNames);
				}

				foreach ($this->rowConfig as $key => $value) {
					$newValue = $this->charConvert($value);
					if (trim($newValue != '')) {
						$value = $newValue;
					}

					// Static values
					if ($key != 'uid') {
						$excelObject->getActiveSheet()->setCellValue($excelColNames[$sheetHeaderCol] . '1', $value);
						$sheetHeaderCols[$sheetHeaderCol] = $colname;
						$sheetHeaderCol++;
					} else {
						if (isset($headerPiVars) && is_array($headerPiVars)) {
							foreach ($headerPiVars as $key => $value) {
								$this->fieldUid = $key;
								$this->getFieldLabelFromBackend();
								$label = $this->charConvert($this->fieldLabel);
								$excelObject->getActiveSheet()->setCellValue($excelColNames[$sheetHeaderCol] . '1', $this->cleanString($this->charConvert($label)));
								$sheetHeaderCols[$sheetHeaderCol] = $colname;
								$sheetHeaderCol++;
							}
						}
					}

				}
				$sheetRow = 2;
			}

			// Generate EXCEL Rows
			$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, 0);
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))) {
				$uploadURLPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $row['uploadPath'];
				if ($row['piVars']) {
					if ($sheetRow == 1) {
						// if no header row was set, generate 1000 ExcelColNames
						$excelColNames = $this->getExcelColNames(1000);
					}
					$i++;
					$piVars = t3lib_div::xml2array($row['piVars'], 'piVars');

					$sheetCol = 0;
					foreach ($this->rowConfig as $key => $value) {
						$colname = $excelColNames[$sheetCol] . $sheetRow;
						// If current row is number
						if ($key == 'number') {
							$excelObject->getActiveSheet()->setCellValue($colname, $i);
							$sheetCol++;

							// If current row is date
						} elseif ($key == 'date') {
							$excelObject->getActiveSheet()->setCellValue($colname, date($this->dateFormat, $row['crdate']));
							$sheetCol++;

							// If current row is time
						} elseif ($key == 'time') {
							$excelObject->getActiveSheet()->setCellValue($colname, date($this->timeFormat, $row['crdate']));
							$sheetCol++;

							// If current row should show all dynamic values (piVars)
						} elseif ($key == 'uid') {
							if (isset($piVars) && is_array($piVars)) {
								// One loop for every piVar
								foreach ($piVars as $key => $value) {
									if (!is_array($value)) {
										$value = $this->charConvert($this->cleanString(t3lib_div::htmlspecialchars_decode($value)));
										switch ($this->formtypes[$key]) {
											case 'date':
												$value = ($value == intval($value) && $value !== '')
														? gmdate($this->dateFormat, $value) : $value;
												break;
											case 'datetime':
												$value = ($value == intval($value) && $value !== '')
														? gmdate($this->datetimeFormat, $value) : $value;
												break;
											case 'file':
												$value = $uploadURLPath . $value;
												break;
										}
										$excelObject->getActiveSheet()->setCellValue($excelColNames[$sheetCol] . $sheetRow, $value);
									} else {

										$xlsContentSecondLevel = array();
										// One loop for every piVar in second level
										foreach ($piVars[$key] as $value2) {
											if ($value2 != '') {
												$xlsContentSecondLevel[] .= $this->charConvert($this->cleanString(t3lib_div::htmlspecialchars_decode($value2)));
											}
										}
										$excelObject->getActiveSheet()->setCellValue($excelColNames[$sheetCol] . $sheetRow, implode(', ', $xlsContentSecondLevel));
									}
									$sheetCol++;
								}
								$sheetCol += intval(count($headerPiVars) - count($piVars));
							}

							// Dynamic value like uid45
						} elseif (is_numeric(str_replace(array('uid', '_'), '', $key))) {

							// Explode uid44_0 to uid44 and 0
							$newkey = explode('_', $key);
							// piVars in first level
							$orgkey = $this->getOriginalLanguageFieldUid($piVars, $key);
							if (!is_array($piVars[$key]) && !is_array($piVars[$orgkey])) {
								$piVars[$key] = trim($piVars[$key]);
								// If $piVars[$key] is empty lookup for $key from original language
								if ($piVars[$key] == '') {
									$key = $this->getOriginalLanguageFieldUid($piVars, $key);
								}
								//$excelObject->getActiveSheet()->setCellValue($colname, $this->charConvert($this->cleanString(t3lib_div::htmlspecialchars_decode($piVars[$key]))));

								$value = $this->charConvert($this->cleanString(t3lib_div::htmlspecialchars_decode($piVars[$key])));
								switch ($this->formtypes[$key]) {
									case 'date':
										$value = ($value == intval($value) && $value !== '')
												? gmdate($this->dateFormat, $value) : $value;
										break;
									case 'datetime':
										$value = ($value == intval($value) && $value !== '')
												? gmdate($this->datetimeFormat, $value) : $value;
										break;
									case 'file':
										$value = $uploadURLPath . $value;
										break;
								}
								$excelObject->getActiveSheet()->setCellValue($excelColNames[$sheetCol] . $sheetRow, $value);

								// PiVars in second level
							} else {
								if ($orgkey != $key) {
									$newkey = explode('_', $orgkey);
								}
								$excelContentSecondLevel = array();
								foreach ($piVars[$newkey[0]] as $key2 => $value2) {
									if ($value2 != '') {
										$excelContentSecondLevel[] .= $this->charConvert($this->cleanString(t3lib_div::htmlspecialchars_decode($value2)));
									}
								}
								$csvContent .= '"' . implode(', ', $excelContentSecondLevel) . '"' . $this->seperator;
								$excelObject->getActiveSheet()->setCellValue($colname, implode(', ', $excelContentSecondLevel));
							}
							$sheetCol++;

						} else {
							$excelObject->getActiveSheet()->setCellValue($colname, $this->charConvert($row[$key]));
							$sheetCol++;
						}
					}
					$sheetRow++;
				}
			}

			if ($this->xlsAutoSize) {
				// Set width of all columns to autosize
				for ($autosize = 0; $autosize < $sheetCol; $autosize++) {
					$excelObject->getActiveSheet()->getColumnDimension($excelColNames[$autosize])->setAutoSize(true);
				}
			}

			$this->tempFilename = t3lib_div::tempnam($this->extKey);

			if ($this->xlsFileFormat != 'Excel2007') {
				// Save Excel 5 file
				$objWriter = new PHPExcel_Writer_Excel5($excelObject);
				$objWriter->save($this->tempFilename);
			} else {
				// Save Excel 2007 file
				$objWriter = new PHPExcel_Writer_Excel2007($excelObject);
				$objWriter->save($this->tempFilename);
			}
			$this->content = file_get_contents($this->tempFilename);
			t3lib_div::unlink_tempfile($this->tempFilename);
		}
	}

	/**
	 * Set output encoding and stores result in $this->outputEncoding
	 *
	 * @return	void
	 */
	protected function setEncoding() {
		// Define output encoding -> No encoding is defined, set default
		if (empty($this->tsConfig['properties']['config.']['export.'][$this->export . '.']['encoding'])) {
			if ($this->export == 'csv') {
				$this->outputEncoding = $this->csvDefaultEncoding;
			} else {
				// Take standard charset from BE
				$this->outputEncoding = $this->LANG->charSet;
			}
		} else {
			$this->outputEncoding = $this->tsConfig['properties']['config.']['export.'][$this->export . '.']['encoding'];
		}
		if ($this->debug) {
			t3lib_div::devLog('outputEncoding was set to ' . $this->outputEncoding, $this->extKey, 0);
		}
	}

	/**
	 * clean up file name for export
	 *
	 * @param	string	$raw is the filename to clean
	 * @return	string	the new filename
	 */
	protected function cleanFileName($raw) {
		$raw = $this->LANG->csConvObj->specCharsToASCII($this->LANG->charSet, trim($raw));
		$removeChars = array("([\40])", "([^a-zA-Z0-9-_])", "(-{2,})");
		$replaceWith = array("-", "", "-");
		return preg_replace($removeChars, $replaceWith, $raw);
	}

	/**
	 * Set filename for export and stores result in $this->filename
	 *
	 * @return	void
	 */
	protected function setFilenames() {
		// overwrite filename if wanted
		if (!empty($this->overwriteFilename)) {
			$this->filename = $this->overwriteFilename;
		} else {
			// create filename
			switch ($this->export) {
				case 'xls':
				case 'email_xls':
					$this->filename = $this->cleanFileName($this->pageTitle . $this->timeFilePrefix) . $this->xlsFileSuffix;
					break;
				case 'csv':
				case 'email_csv':
					$this->filename = $this->cleanFileName($this->pageTitle . $this->timeFilePrefix) . $this->csvFileSuffix;
					break;
				case 'html':
				case 'email_html':
					$this->filename = $this->cleanFileName($this->pageTitle . $this->timeFilePrefix) . $this->htmlFileSuffix;
					break;
			}
		}
	}

	/**
	 * Set time date format and stores result in $this->timeFormat
	 *
	 * @return	void
	 */
	protected function setDateTimeFormat() {
		if (!empty($this->tsConfig['properties']['config.']['export.']['dateformat'])) {
			$this->dateFormat = $this->tsConfig['properties']['config.']['export.']['dateformat'];
		}
		if ($this->debug) t3lib_div::devLog('dateFormat was set to ' . $this->dateFormat, $this->extKey, 0);

		if (!empty($this->tsConfig['properties']['config.']['export.']['timeformat'])) {
			$this->timeFormat = $this->tsConfig['properties']['config.']['export.']['timeformat'];
		}
		if ($this->debug) t3lib_div::devLog('timeFormat was set to ' . $this->timeFormat, $this->extKey, 0);
	}

	/**
	 * Compress a file (not used any more)
	 *
	 * @param	string		$source Defines Source file to compress
	 * @param	boolean		$level	 Defines Compression level
	 * @return	string
	 */
	protected function gzcompressfile($source, $level = false) {
		$dest = $source . '.gz';
		$mode = 'wb' . $level;
		$error = false;

		if (($fp_out = gzopen($dest, $mode))) {
			if (($fp_in = fopen($source, 'rb'))) {
				while (!feof($fp_in)) {
					gzwrite($fp_out, fread($fp_in, 1024 * 512));
				}

				fclose($fp_in);
			} else {
				$error = true;
			}
			gzclose($fp_out);

		} else {
			$error = true;
		}

		if ($error) {
			return false;
		} else {
			return $dest;
		}
	}

	/**
	 * Generate header and stores result in $this->header
	 *
	 * @return	void
	 */
	protected function generateFileHeader() {
		if (strstr(t3lib_div::getIndpEnv('HTTP_USER_AGENT'), 'MSIE')) {
			$this->header .= header('Content-Type: application/force-download; charset=' . $this->outputEncoding);
			$this->header .= header('Content-Disposition: attachment; filename="' . $this->filename . '"');
		} else {
			switch ($this->export) {
				case 'xls':
					if ($this->xlsFileFormat == 'Excel2007') {
						$contenttype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=' . $this->outputEncoding;
					} else {
						$contenttype = 'application/vnd.ms-excel; charset=' . $this->outputEncoding;
					}
					break;
				default:
					$contenttype = 'text/csv; charset=' . $this->outputEncoding;
			}
			$this->header .= header('Content-Type: ' . $contenttype);
			$this->header .= header('Content-Disposition: inline; filename="' . $this->filename . '"');
		}

		if ($this->debug) {
			t3lib_div::devLog('Header Content-Type was set to ' . $contenttype, $this->extKey, 0);
			t3lib_div::devLog('Header filename was set to ' . $this->filename, $this->extKey, 0);
			t3lib_div::devLog('Temporary filename was set to ' . $this->tempFilename, $this->extKey, 0);
		}

		$this->header .= header('Expires: Fri, 01 Jan 2010 05:00:00 GMT');
		if (strstr(t3lib_div::getIndpEnv('HTTP_USER_AGENT'), 'MSIE') == false) {
			$this->header .= header('Cache-Control: no-cache');
			$this->header .= header('Pragma: no-cache');
		} else {
			// Fixes a bug which won't allow a XLS download to start in IE with SSL on
			$this->header .= header('Pragma: public');
			$this->header .= header('Cache-Control: private');
		}
	}

	/**
	 * Save content into TYPO3 temp dir
	 *
	 * @return	void
	 */
	protected function writeContentToTypo3tempDir() {
		t3lib_div::writeFileToTypo3tempDir($this->absFilePath . $this->filename, $this->content);
	}

	/**
	 * Method getRowWithMostPiVars() to find the record with the most piVars for generating correct header
	 *
	 * @return	integer
	 */
	protected function getRowWithMostPiVars() {
		// Find the record with the most piVars for generating correct header
		$rowCounter = 0;
		$mostPiVars = 0;
		$rowWithMostPiVars = 0;
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, 0);
		while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))) {
			if ($row['piVars']) {
				$piVars = t3lib_div::xml2array($row['piVars'], 'piVars');
				$countPiVars = count($piVars);
				if ($countPiVars > $mostPiVars && $this->piVarsFromExportLanguageUid($piVars)) {
					$mostPiVars = $countPiVars;
					$rowWithMostPiVars = $rowCounter;
				}
				$rowCounter++;
			}
		}
		return $rowWithMostPiVars;
	}

	/**
	 * Method piVarsFromExportLanguageUid() check if piVars from exportHeaderLanguageUid
	 *
	 * @param	array	$piVars this are the piVars to check
	 * @return	boolean	returns true if piVars are from export header language uid
	 */
	protected function piVarsFromExportLanguageUid($piVars) {
		$piVarsKeys = array_keys($piVars);
		$fieldUid = $piVarsKeys[0];
		$fieldUidInt = intval(str_replace(array('uid', '_'), '', $fieldUid));
		$select = 'uid';
		$from = 'tx_powermail_fields';
		$where = 'pid = ' . $this->pid . ' AND uid = ' . $fieldUidInt . ' AND sys_language_uid = ' . $this->exportHeaderLanguageUid . $this->generalRecordsFilter;
		$groupBy = '';
		$orderBy = '';
		$limit = '1';
		$lookupRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$lookupRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($lookupRes);
		//if($this->debug) t3lib_div::devLog('SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where . ' LIMIT ' . $limit, $this->extKey, 0);
		return $lookupRow ? true : false;
	}

	/**
	 * generateFormtypesArray method			Generate form types array of powermail on selected page as array
	 *
	 * @return	void
	 */
	protected function generateFormtypesArray() {
		$this->formtypes = array();

		$select = 'uid,formtype';
		$from = 'tx_powermail_fields';
		//$where = 'pid = ' . intval($this->pid);
		$where = '';
		$orderBy = '';
		$groupBy = '';
		$limit = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		if ($res !== false) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->formtypes['uid' . $row['uid']] = $row['formtype'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
	}

	/**
	 * Method getFieldLabelFromBackend() to get label to current field for emails and thx message
	 *
	 */
	protected function getFieldLabelFromBackend() {

		$this->fieldLabel = $this->fieldUid;

		if (strpos($this->fieldUid, 'uid') !== FALSE) {
			$uid = intval(str_replace('uid', '', $this->fieldUid));

			$select = 'f.title, f.formtype';
			$from = '
				tx_powermail_fields f
				LEFT JOIN tx_powermail_fieldsets fs
				ON (
					f.fieldset = fs.uid
				)
				LEFT JOIN tt_content c
				ON (
					c.uid = fs.tt_content
				)';
			$where = '
				c.deleted = 0
				AND c.hidden = 0
				AND (
					c.starttime <= ' . time() . '
				)
				AND (
					c.endtime = 0
					OR c.endtime>' . time() . '
				)
				AND (
					c.fe_group = ""
					OR c.fe_group IS NULL
					OR c.fe_group = "0"
					OR (
						c.fe_group LIKE "%,0,%"
						OR c.fe_group LIKE "0,%"
						OR c.fe_group LIKE "%,0"
						OR c.fe_group = "0"
					)
					OR (
						c.fe_group LIKE "%,-1,%"
						OR c.fe_group LIKE "-1,%"
						OR c.fe_group LIKE "%,-1"
						OR c.fe_group = "-1"
					)
				)
				AND f.uid = ' . $uid . '
				AND f.deleted = 0';

			// GET title where fields.flexform LIKE <value index="vDEF">vorname</value>
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);

			if ($res !== false) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				if (count($row) > 0) {
					if (!empty($row['title'])) {
						$this->fieldLabel = $row['title'];
						$this->fieldType = $row['formtype'];
					} else if ($uid >= 100000) {
						// check for country select
						$this->fieldUid = 'uid' . ($uid - 100000);
						$this->getFieldLabelFromBackend();
						if ($this->fieldType == 'countryselect') {
							$this->fieldLabel = sprintf($this->tsConfig['properties']['config.']['export.'][$this->export . '.']['country_zone_label'], $this->fieldLabel);
						}
					}
				}
			}
		}
	}

	/**
	 * Method getOriginalLanguageFieldUid() get the original language field uid
	 *
	 * @param	array	$piVars this are the original piVars
	 * @param	string	$fieldUid this is the original field uid with 'uid' prefix
	 * @param	string	$fieldUidLevel2 this is the original field uid from level 2 (used by checkboxes and multiselect)
	 * @return	string	returns the new field uid with 'uid' prefix
	 */
	protected function getOriginalLanguageFieldUid($piVars, $fieldUid, $fieldUidLevel2 = '') {
		$newFieldUid = $fieldUid;
		$fieldUidInt = intval(str_replace(array('uid', '_'), '', $fieldUid));
		$select = 'uid';
		$from = 'tx_powermail_fields';
		$where = 'pid = ' . $this->pid . ' AND l18n_parent = ' . $fieldUidInt . $this->generalRecordsFilter;
		$groupBy = '';
		$orderBy = '';
		$limit = '1';
		$lookupRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
		$lookupRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($lookupRes);
		if (trim($piVars['uid' . $lookupRow['uid']]) != '' || ($fieldUidLevel2 != '')) {
			$newFieldUid = 'uid' . $lookupRow['uid'];
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($lookupRes);
		return $newFieldUid;
	}

	/**
	 * Method charConvert() converts a string from backend charset to outputEncoding charset
	 *
	 * @param	string	$string2convert this is the string to convert
	 * @return	string	returns the converted value
	 */
	protected function charConvert($string2convert) {
		return $this->LANG->csConvObj->conv($string2convert, $this->LANG->charSet, $this->outputEncoding);
	}

	/**
	 * Method cleanString() cleans up a string
	 *
	 * @param	string	$string2clean this is the string to clean
	 * @return	string	returns the cleaned string
	 */
	protected function cleanString($string2clean) {
		switch ($this->export) {
			case 'csv':
				$string2clean = str_replace(array("\n\r", "\r\n", "\n", "\r"), '', $string2clean);
				$string2clean = str_replace('"', "'", $string2clean);
				$string2clean = stripslashes($string2clean);
				break;
			default:
				$string2clean = stripslashes($string2clean);
		}
		return $string2clean;
	}

	/**
	 * Method getExcelColNames() returns an array with Excel column names like A or AA
	 *
	 * @param	integer		$cols this is the number of cols who should be generated
	 * @return	array	returns the excelColNames array;
	 */
	protected function getExcelColNames($cols = 1000) {
		$excelColNames = array();
		for ($excelCol = 0; $excelCol < $cols; $excelCol++) {
			$excelColNames[] .= $this->num2alpha($excelCol);
		}
		return $excelColNames;
	}

	/**
	 * Method num2alpha() returns a string with a Excel column name like A or AA of a give column position
	 *
	 * @param	integer	position which should be converted to a excel column value
	 * @return	string	returns the excel column value;
	 */
	protected function num2alpha($n) {
		for ($r = ''; $n >= 0; $n = intval($n / 26) - 1) {
			$r = chr($n % 26 + 0x41) . $r;
		}
		return $r;
	}

	// Method hook_exportclass allows to manipulate config at the point of exporting
	protected function hook_exportclass() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_exportConfigHook'])) { // Adds hook for processing
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_exportConfigHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->PM_exportConfigHook($this); // Get new marker Array from other extensions
			}
		}
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']);
}
?>