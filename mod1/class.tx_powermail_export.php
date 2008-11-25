<?php
require_once('../lib/class.tx_powermail_functions_div.php'); // include div functions

class tx_powermail_export {

	var $extKey = 'powermail'; // Extension key
	var $dateformat = 'Y-m-d'; // timeformat for displaying date
	var $timeformat = 'H:i:s'; // timeformat for displaying date
	var $seperator = ';'; // separator for csv
	var $csvfilename = 'powermail_export.csv'; // filename of exported CSV file
	var $zip = 1; // activate CSV file compressing to .gz
	var $rowconfig = array('number'=>'#', 'date'=>'Date', 'time'=>'Time', 'uid'=>'all', 'sender'=>'Sender email', 'senderIP'=>'Sender IP address', 'recipient'=>'Recipient email', 'subject_r'=>'Email subject', 'formid'=>'Page ID', 'UserAgent'=>'UserAgent', 'Referer'=>'Referer', 'SP_TZ'=>'Sender location'); // set order for export
	var $LANG; // Local copy of lang object
	var $outputEncoding; // Encoding for data output
	var $tsconfig; // Module's TSconfig

	// Function Main
	function main($export, $pid = 0, $LANG = '') {
		// config
		$this->pid = $pid; // Page ID
		$this->startdate = $_GET['startdate']; // startdate from GET var
		$this->enddate = $_GET['enddate']; // enddate from GET var
		$this->LANG = $LANG; // make $LANG global
		$content = ''; $i = 0; // init 
		$this->tsconfig = t3lib_BEfunc::getModTSconfig($this->pid,'tx_powermail_mod1'); // Get tsconfig from current page
		!empty($this->tsconfig['properties']['config.']['export.']['dateformat']) ? $this->dateformat = $this->tsconfig['properties']['config.']['export.']['dateformat'] : ''; // set dateformat
		!empty($this->tsconfig['properties']['config.']['export.']['timeformat']) ? $this->timeformat = $this->tsconfig['properties']['config.']['export.']['timeformat'] : ''; // set timeformat
		$this->tsconfig['properties']['config.']['export.']['useTitle'] == 0 && isset($this->tsconfig['properties']['config.']['export.']['useTitle']) ? $this->useTitle = $this->tsconfig['properties']['config.']['useTitle'] : $this->useTitle = 1; // titles should be set
		count($this->tsconfig['properties']['export.']) > 0 ? $this->rowconfig = $this->tsconfig['properties']['export.'] : ''; // overwrite rowconfig if set
		if (empty($this->tsconfig['properties']['config.']['export.']['encoding.'][$export])) { // Define output encoding -> No encoding is defined, set default
			if ($export == 'csv') $this->outputEncoding = 'latin1'; // Set LATIN1 for csv
			else $this->outputEncoding = $this->LANG->charSet; // Take standard charset
		} else $this->outputEncoding = $this->tsconfig['properties']['config.']['export.']['encoding.'][$export]; // Take charset from tsconfig

		// DB query
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_powermail_mails',
			$where_clause = 'pid = '.$this->pid.' AND hidden = 0 AND deleted = 0 AND crdate > '.strtotime($this->startdate).' AND crdate < '.strtotime($this->enddate),
			$groupBy = '',
			$orderBy = 'crdate DESC',
			$limit = ''
		);
		if ($res) { // If on current page is a result
			if($export == 'xls' || $export == 'table') { // if Excel export or HTML Table
				$table = '<table>'; // Init table
				$table .= $this->setTitle($export,$row); // Title
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
					if($row['piVars']) {
						if ($this->outputEncoding != 'utf-8') $row['piVars'] = $this->LANG->csConvObj->conv($row['piVars'], $this->LANG->charSet, 'utf-8'); // change to utf8 to avoid problems with umlauts
						$values = t3lib_div::xml2array($row['piVars'], 'piVars'); // xml2array
						
						$i++; // increase counter
						$table .= '<tr>';
						foreach ($this->rowconfig as $key => $value) { // every row from config
							if ($key == 'number') $table .= '<td>'.$i.'.</td>'; // if current row is number
							elseif ($key == 'date') $table .= '<td>'.date($this->dateformat, $row['crdate']).'</td>'; // if current row is date
							elseif ($key == 'time') $table .= '<td>'.date($this->timeformat, $row['crdate']).'</td>'; // if current row is time
							elseif ($key == 'uid') { // if current row should show all dynamic values (piVars)
								if(isset($values) && is_array($values)) {
									foreach ($values as $key => $value) { // one loop for every piVar
										if (!is_array($value)) $table .= '<td>'.$this->cleanString($value, $export).'</td>';
										else {
											foreach ($values[$key] as $key2 => $value2) { // one loop for every piVar in second level
												$table .= '<td>'.$this->cleanString($value2, $export).'</td>';
											}
										}
									}
								}
							}
							elseif (is_numeric(str_replace(array('uid','_'),'',$key))) { // dynamic value like uid45
								$newkey = explode('_',$key); // explode uid44_0 to uid44 and 0
								if (!is_array($values[$newkey[0]])) { // piVars in first level
									if (!empty($values[$key])) { // if is set
										$table .= '<td>'.$this->cleanString($values[$key], $export).'</td>'; // fill cell with content
									} else {
										$table .= '<td></td>'; // empty cell
									}
								} else { // piVars in second level
									if (!empty($values[$newkey[0]][$newkey[1]])) { // if is set
										$table .= '<td>'.$this->cleanString($values[$newkey[0]][$newkey[1]], $export).'</td>'; // fill cell with content
									} else {
										$table .= '<td></td>'; // empty cell
									}
								}
							}
							else $table .= '<td>'.$row[$key].'</td>';
						}
						$table .= '</tr>';
					}
				}
				$table .= '</table>';
			} elseif ($export == 'csv') { // if CSV Export
				//$table .= 'sep=,'."\n"; // write first line
				$table .= $this->setTitle($export,$row); // Title
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // one loop for every db entry
					if($row['piVars']) {
						$i++; // increase counter
						if ($this->LANG->charSet != 'utf-8') $row['piVars'] = $this->LANG->csConvObj->conv($row['piVars'], $this->LANG->charSet, 'utf-8'); // change to utf8 to avoid problems with umlauts
						$values = t3lib_div::xml2array($row['piVars'], 'piVars'); // xml2array
						//if ($this->outputEncoding != 'utf-8') $this->LANG->csConvObj->convArray($values, 'utf-8', $this->outputEncoding);
						
						
						foreach ($this->rowconfig as $key => $value) { // every row from config
							if ($key == 'number') $table .= '"'.$i.'."'.$this->seperator; // if current row is number
							elseif ($key == 'date') $table .= '"'.date($this->dateformat, $row['crdate']).'"'.$this->seperator; // if current row is date
							elseif ($key == 'time') $table .= '"'.date($this->timeformat, $row['crdate']).'"'.$this->seperator; // if current row is time
							elseif ($key == 'uid') { // if current row should show all dynamic values (piVars)
								if(isset($values) && is_array($values)) {
									foreach ($values as $key => $value) { // one loop for every piVar
										//if(!is_array($value)) $table .= '"'.str_replace('"',"'",str_replace(array("\n\r","\r\n","\n","\r"),'',$value)).'"'.$this->seperator;
										if(!is_array($value)) $table .= '"'.$this->cleanString($value).'"'.$this->seperator;
										else {
											foreach ($values[$key] as $key2 => $value2) { // one loop for every piVar in second level
												//$table .= '"'.str_replace('"',"'",str_replace(array("\n\r","\r\n","\n","\r"),'',$value2)).'"'.$this->seperator;
												$table .= '"'.$this->cleanString($value2).'"'.$this->seperator;
											}
										}
									}
								}
							}
							elseif (is_numeric(str_replace(array('uid','_'),'',$key))) { // dynamic value like uid45
								$newkey = explode('_',$key); // explode uid44_0 to uid44 and 0
								if (!is_array($values[$newkey[0]])) { // piVars in first level
									if (!empty($values[$key])) { // if is set
										$table .= '"'.$this->cleanString($values[$key]).'"'.$this->seperator; // fill cell with content
									} else {
										$table .= '" "'.$this->seperator; // empty cell
									}
								} else { // piVars in second level
									if (!empty($values[$newkey[0]][$newkey[1]])) { // if is set
										$table .= '"'.$this->cleanString($values[$newkey[0]][$newkey[1]]).'"'.$this->seperator; // fill cell with content
									} else {
										$table .= '" "'.$this->seperator; // empty cell
									}
								}
							}
							else $table .= '"'.$row[$key].'"'.$this->seperator;
						}
						$table = substr($table,0,-1); // delete last ,
						$table .= "\n"; // new line
					}
				}
			}
		}
		
		// What to show
		if($export == 'xls') {
			$content .= header("Content-type: application/vnd-ms-excel");
			$content .= header("Content-Disposition: attachment; filename=export.xls");
			$content .= $table; // add table to content
		
		} elseif ($export == 'csv') {
		
			if(!t3lib_div::writeFileToTypo3tempDir(PATH_site.'typo3temp/'.$this->csvfilename,$table)) { // write to typo3temp and if success returns FALSE
				$content .= '<strong>'.$this->LANG->getLL('export_download_success').'</strong><br />';
				$this->gzcompressfile(PATH_site.'typo3temp/'.$this->csvfilename); // compress file
				$content .= '<a href="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'/typo3temp/'.$this->csvfilename.'" target="_blank"><u>'.$this->LANG->getLL('export_download_download').'</u></a><br />'; // link to xx.csv.gz
				$content .= '<a href="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'/typo3temp/'.$this->csvfilename.'.gz" target="_blank"><u>'.$this->LANG->getLL('export_download_downloadZIP').'</u></a><br />'; // link to xx.csv
			} else {
				$content .= t3lib_div::writeFileToTypo3tempDir(PATH_site.'typo3temp/'.$this->csvfilename,$table);
			}
		
		} elseif($export == 'table') {
		
			$content .= $table; // add table to content
		
		} else { // not supported method
			$content = 'Wrong export method chosen!';
		}
		
		if ($_GET['delafterexport']==1) { // delete all exported mails now
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery ( // deleted = 1 in db
				'tx_powermail_mails',
				'pid = '.$this->pid.' AND hidden = 0 AND deleted = 0 AND crdate > '.strtotime($this->startdate).' AND crdate < '.strtotime($this->enddate),
				array (
					'deleted' => 1
				)
			);
		}
		
		
		return $content;
	}

	// Compress a file
	function gzcompressfile($source,$level=false){ 
		$dest = $source.'.gz';
		$mode = 'wb'.$level;
		$error = false;
		if($fp_out=gzopen($dest,$mode)){
			if($fp_in=fopen($source,'rb')){
				while(!feof($fp_in))
				gzwrite($fp_out,fread($fp_in,1024*512));
				fclose($fp_in);
			}
			else $error=true;
			gzclose($fp_out);
		}
		else $error=true;
		
		if($error) return false;
		else return $dest;
	}
	
	
	// Set title (TODO: extend to dynamic Titles)
	function setTitle($export, $row) {
		if ($this->useTitle == 1 && isset($this->rowconfig)) {	// if title should be used
			if ($this->LANG->charSet != 'utf-8') $row['piVars'] = $this->LANG->csConvObj->conv($row['piVars'], $this->LANG->charSet, 'utf-8');
			$values = t3lib_div::xml2array($row['piVars'], 'pivars'); // xml2array
			
			($export == 'csv' ? $table = '' : $table = '<tr>'); // init
			foreach ($this->rowconfig as $key => $value) { // one loop for every row
				if ($this->outputEncoding != 'utf-8') $value = $this->LANG->csConvObj->conv($value, 'utf-8', $this->outputEncoding);
				if ($key != 'uid') { // static values
					if ($export == 'csv') { // CSV only
						$table .= '"'.$value.'"'.$this->seperator;
					} else { // HTML and EXCEL only
						$table .= '<td><b>'.$value.'</b></td>';
					}
				} else {
					if(isset($values) && is_array($values)) {
						foreach ($values as $key => $value) { // one loop for every piVar
							//if (!is_array($value) && $export == 'csv') $table .= '"'.str_replace('"',"'",str_replace(array("\n\r","\r\n","\n","\r"),'', $this->GetLabelfromBackend($key, $value))).'"'.$this->seperator;
							//elseif (!is_array($value)) $table .= '<td>'.$this->GetLabelfromBackend($key, $value).'</td>';
							$label = $this->GetLabelfromBackend($key, $value);
							if ($this->outputEncoding != 'utf-8') $label = $this->LANG->csConvObj->conv($label, 'utf-8', $this->outputEncoding);
							if (!is_array($value)) {
								if ($export == 'csv') {
									$table .= '"'.$this->cleanString($label).'"'.$this->seperator;
								}
								else {
									$table .= '<td>'.$label.'</td>';
								}
							}
						}
					}
				}
			}
			($export == 'csv' ? $table = substr($table,0,-1)."\n" : $table .= '</tr>'); // init
			if (!empty($table)) return $table;
		}
	}
    
	
    // Function GetLabelfromBackend() to get label to current field for emails and thx message
    function GetLabelfromBackend($name,$value) {
		if(strpos($name,'uid') !== FALSE) { // $name like uid55
			$uid = str_replace('uid','',$name);

			$where_clause = 'c.deleted=0 AND c.hidden=0 AND (c.starttime<='.time().') AND (c.endtime=0 OR c.endtime>'.time().') AND (c.fe_group="" OR c.fe_group IS NULL OR c.fe_group="0" OR (c.fe_group LIKE "%,0,%" OR c.fe_group LIKE "0,%" OR c.fe_group LIKE "%,0" OR c.fe_group="0") OR (c.fe_group LIKE "%,-1,%" OR c.fe_group LIKE "-1,%" OR c.fe_group LIKE "%,-1" OR c.fe_group="-1"))'; // enable fields for tt_content
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ( // GET title where fields.flexform LIKE <value index="vDEF">vorname</value>
				'f.title',
				'tx_powermail_fields f LEFT JOIN tx_powermail_fieldsets fs ON (f.fieldset = fs.uid) LEFT JOIN tt_content c ON (c.uid = fs.tt_content)',
				$where_clause .= ' AND f.uid = '.$uid.' AND f.hidden = 0 AND f.deleted = 0',
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);
			if ($res) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			if(isset($row['title'])) return $row['title']; // if title was found return ist
			else if ($uid < 100000) return 'POWERMAIL ERROR: No title to current field found in DB'; // if no title was found return 
		} else { // no uid55 so return $name
			return $name;
		}
    }
	
	
	// Function cleanString() cleans up a string
	function cleanString($string, $export = 'csv') {
		if ($export == 'csv') { // csv
			$string = str_replace(array("\n\r","\r\n","\n","\r"), '', $string);
			$string = str_replace('"', "'", $string);
			$string = stripslashes($string);
			if ($this->outputEncoding == 'utf-8') $string = utf8_decode($string); // if utf8 - decode for CSV
		} elseif ($export == 'xls') { // xls
			$string = stripslashes($string);
			if ($this->outputEncoding == 'utf-8') $string = utf8_decode($string); // if utf8 - decode for Excel
		} else { // table
			$string = stripslashes($string);
		}
		
    	return $string;
    }

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/mod1/class.tx_powermail_export.php']);
}
?>