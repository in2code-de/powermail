<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 powermail development team (details on http://forge.typo3.org/projects/show/extension-powermail)
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

/**
 * Class with collection of different functions (like string and array functions)
 *
 * @author	Alex Kellner <alexander.kellner@in2code.de>
 * @package	TYPO3
 * @subpackage	tx_powermail_functions_div
 */
class tx_powermail_functions_div {

	public $extKey = 'powermail';
    public $confArr;
	/**
	 * Function sec() is a security function against all bad guys :)
	 *
	 * @param	array		$array: Unfiltered piVars Array
	 * @return	array		$array: Filtered piVars Array
	 */
	public function sec($array) {
		if (isset($array) && is_array($array)) { // if array
			//$this->removeXSS = t3lib_div::makeInstance('tx_powermail_removexss'); // New object: removeXSS function
			//t3lib_div::addSlashesOnArray($array); // addslashes for every piVar (He'l"lo => He\'l\"lo)

			foreach ($array as $key => $value) { // one loop for every key in first level

				if (!is_numeric(str_replace('UID', '', $key)) && !is_array($value)) { // all others piVars than UID34
					$array[$key] = intval(trim($value)); // the value should be integer
				}

				if (!is_array($value)) {	// if value is not an array

					$array[$key] = strip_tags(trim($value)); // strip_tags removes html and php code
					$array[$key] = htmlspecialchars($array[$key]); // use addslashes
					//$array[$key] = $this->removeXSS->RemoveXSS($array[$key]); // use remove XSS for piVars

				} else { // value is still an array (second level)

					if (!is_array($key2)) {	// if value is not an array
						foreach ($value as $key2 => $value2) { // one loop for every key in second level

							$array[$key][$key2] = strip_tags(trim($value2)); // strip_tags removes html and php code
							$array[$key][$key2] = htmlspecialchars($array[$key][$key2]); // use addslashes
							//$array[$key][$key2] = $this->removeXSS->RemoveXSS($array[$key][$key2]); // use remove XSS for piVars

						}
					} else unset($array[$key][$key2]); // if array with 3 or more dimensions - delete this value

				}
			}

			return $array;

		}
	}

	/**
	 * Add debug view for any array
	 *
	 * @param	array		$array: array for output
	 * @param	string		$msg: Message
	 * @return	void
	 */
	public function debug($array, $msg = 'Debug output') {
		t3lib_div::debug($array, $this->extKey . ': ' . $msg); // debug output
	}

	/**
	 * Function clearName() to disable not allowed letters (only A-Z and 0-9 allowed) (e.g. Perfect Extension -> perfectextension)
	 *
	 * @param	string		$string: String to change
	 * @param	boolean		$strtolower: Should the string be changed to lower characters?
	 * @param	int			$cut: Should the string cutted after X signs?
	 * @return	string		$string: Manipulated string
	 */
	public function clearName($string, $strtolower = 0, $cut = 0) {
		$string = preg_replace('/[^a-zA-Z0-9]/', '', $string); // replace not allowed letters with nothing
		if ($strtolower) $string = strtolower($string); // string to lower if active
		if ($cut) $string = substr($string, 0, $cut); // cut after X signs if active

		if (!empty($string)) return $string;
	}

	/**
	 * Function clearValue() to remove all " or ' from any string
	 *
	 * @param	string		$string: string input
	 * @param	boolean		$htmlspecialchars: (De)activate htmlspecialchars
	 * @param	boolean		$strip_tags: (De)activate strip_tags
	 * @return	string		$string: Filtered string
	 */
	public function clearValue($string, $htmlspecialchars = 1, $strip_tags = 0) {
		$notallowed = array('"', "'");
		$string = str_replace($notallowed, '' , $string); // replace not allowed letters with nothing
		if ($htmlspecialchars) {
			$string = htmlspecialchars($string); // change code to ascii code
		}
		if ($strip_tags) {
			$string = strip_tags($string); // disable html/php code
		}

		return $string;
	}

	/**
	 * Function linker() generates link (email and url) from pure text string within an email or url
	 * 'test www.test.de test' => 'test <a href="http://www.test.de">www.test.de</a> test'
	 *
	 * @param	string		$link: string input with links
	 * @param	string		$additinalParams: Without function
	 * @return	string		$link: string with links
	 */
	public function linker($link, $additinalParams = '') {
        $link = str_replace('http://www.', 'www.', $link);
        $link = str_replace('www.', 'http://www.', $link);
        $link = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<a href=\"$1\"$additinalParams>$1</a>", $link);
        $link = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i", "<a href=\"mailto:$1\"$additinalParams>$1</a>", $link);

        return $link;
    }

	/**
	 * Function nl2br2() changes breakes to html breakes
	 *
	 * @param	string		$string: Anystring
	 * @return	string		$string: Manipulated string
	 */
	public function nl2br2($string) {
		$array = array(
			'\r\n',
			'\n',
			'\r',
			'\n\r',
			"\r\n",
			"\n",
			"\r",
			"\n\r"
		);
		return str_replace($array, '<br />', $string);
	}

	/**
	 * Function nl2br2() changes breakes to real breakes
	 *
	 * @param	string		$string: Anystring
	 * @return	string		$string: Manipulated string
	 */
	public function nl2nl2($string) {
		return str_replace('\r\n', "\r\n", $string);
	}

	/**
	 * Function br2nl is the opposite of nl2br
	 *
	 * @param	string		$content: Anystring
	 * @return	string		$content: Manipulated string
	 */
	public function br2nl($content) {
		$array = array(
			'<br >',
			'<br>',
			'<br/>',
			'<br />'
		);
		$content = str_replace($array, "\n", $content); // replacer

		if (!empty($content)) return $content;
	}

	/**
	 * Function correctPath() checks if the link is like "fileadmin/test/ and not "/fileadmin/test"
	 * If there is no Slash at the end of the picture folder, add a slash and if there is a slash at the beginning, remove this slash
	 *
	 * @param	string		$value: Path
	 * @return	string		$value: Manipulated path
	 */
	public function correctPath($value) {
		if (substr($value, -1, 1) != '/') {
			$value .= '/'; // add a slash at the end if there is no slash
		}
		if (substr($value, 0, 1) == '/') {
			$value = substr($value, 1); // remove slash from the front
		}

		if ($value) return $value;
	}

	/**
	 * Function marker2value() replaces ###UID3### with its value from session
	 *
	 * @param	string		$string: Any string
	 * @param	array		$sessiondata: Values from session an an array
	 * @param	boolean		$quoted: Should the string transformed to a quoted list
	 * @return	string		replaced string
	 */
	public function marker2value($string, $sessiondata, $quoted = 0) {
		$this->sessiondata = $sessiondata; // make session array available in other functions
		$this->activateQuotedList = $quoted; // activate intList

		$string = preg_replace_callback ( // Automaticly replace ###UID55### with value from session to use markers in query strings
			'#\#\#\#UID(.*)\#\#\##Uis', // regulare expression
			array($this, 'uidReplaceIt'), // open function
			$string // current string
		);

		return $string;
	}

	/**
	 * Function uidReplaceIt is used for the callback function to replace ###UID55## with it's value
	 *
	 * @param	string		$uid: field uid
	 * @return	string		it's value from the session
	 */
	public function uidReplaceIt($uid) {
		if (strpos($uid[1], '_')  === false) { // if this is a field like ###UID55### and not like ###UID55_1###
			if (isset($this->sessiondata['uid' . $uid[1]])) { // if there is a value in the session like uid32 = bla
				if (!is_array($this->sessiondata['uid' . $uid[1]])) { // value is not an array

					if (!$this->activateQuotedList) { // return without int transformation
						return $this->sessiondata['uid' . $uid[1]]; // return bla (e.g.)
					} else { // transform to quotedList
						return $this->makeQuotedList($this->sessiondata['uid' . $uid[1]]); // return bla (e.g.)
					}

				} else { // value is an array

					$return = ''; $i=0; // init counter
					foreach ($this->sessiondata['uid' . $uid[1]] as $key => $value) { // one loop for every value
						if ($value != '') {
							$return .= ($return != '' ? ',' : '') . (!$this->activateQuotedList ? $value : $this->makeQuotedList($value)); // add a value (commaseparated)
							$i++; // increase counter
						}
					}
					return $return; // return 44,45,46 (e.g.)

				}
			}
		} else { // if this is a field like ###UID55_1###
			$tmp_uid = t3lib_div::trimExplode('_', $uid[1], 1); // spilt at _ to get 55 and 1
			if ($this->sessiondata['uid' . $tmp_uid[0]][$tmp_uid[1]]) { // value is not an array
				if (!$this->activateQuotedList) { // return without transformation
					return $this->sessiondata['uid' . $tmp_uid[0]][$tmp_uid[1]]; // return bla (e.g.)
				} else { // transform to intList
					return $this->makeQuotedList($this->sessiondata['uid' . $tmp_uid[0]][$tmp_uid[1]]); // return bla (e.g.)
				}
			}
		}
	}

	/**
	 * Function makeQuotedList() to transfor string list to an quoted list
	 * 	1,2 => "1","2"
	 * 	value1,value2 => "value1","value2"
	 *
	 * @param	string		$string: string like "1,2,3,4"
	 * @return	string		it's integer values
	 */
	public function makeQuotedList($string) {
		$str_arr = explode(',', $string); // split on ,
		for ($i=0; $i<count($str_arr); $i++) { // one loop for every part of the list
			//$str_arr[$i] = intval($str_arr[$i]); // transform to integer
			$str_arr[$i] = $GLOBALS['TYPO3_DB']->fullQuoteStr($str_arr[$i], true); // quote current strting
		}
		$list = implode(',', $str_arr); // merge with , as glue
		return $list;
	}

	/**
	 * Function checkMX() checks if a domain exists
	 *
	 * @param	string		$email: string like "aaa@bbb.cc"
	 * @return	boolean
	 */
	public function checkMX($email) {
		if (function_exists('checkdnsrr')) { // if function checkdnsrr() exist (not available on windows systems)
			return !!checkdnsrr(array_pop(explode('@', $email)), 'MX');
		} else { // function checkdnsrr() don't exist
			return TRUE; // so always return TRUE
		}
	}

	/**
	 * Function charset() changes content with utf8_decode or utf8_encode or nothing
	 *
	 * @param	string		$content: any string
	 * @param	string		$function: Should be empty or utf8_encode or utf8_decode
	 * @return	string		$content
	 */
	public function charset($content, $function = '') {
		switch ($function) {
			case 'utf8_encode': // utf8_encode
				$content = utf8_encode($content); // encode
				break;

			case 'utf8_decode': // utf8_decode
				$content = utf8_decode($content); // decode
				break;

			default: // if empty or something else
				$content = $content; // do nothing
				break;
		}

		if (!empty($content)) return $content;
	}

	/**
	 * Function makePlain() removes html tags and add linebreaks
	 * Easy generate a plain email bodytext from a html bodytext
	 *
	 * @param	string		$content: HTML Mail bodytext
	 * @return	string		$content: Plain Mail bodytext
	 */
	public function makePlain($content) {
		// config
		$htmltagarray = array ( // This tags will be added with linebreaks
			'</p>',
			'</tr>',
			'</li>',
			'</h1>',
			'</h2>',
			'</h3>',
			'</h4>',
			'</h5>',
			'</h6>',
			'</div>',
			'</legend>',
			'</fieldset>',
			'</dd>',
			'</dt>'
		);
		$notallowed = array ( // This array contains not allowed signs which will be removed
			'&nbsp;',
			'&szlig;',
			'&Uuml;',
			'&uuml;',
			'&Ouml;',
			'&ouml;',
			'&Auml;',
			'&auml;',
		);

		// let's go
		$content = str_replace($htmltagarray, $htmltagarray[0].'<br />', $content); // 1. add linebreaks on some parts (</p> => </p><br />)
		$content = strip_tags($content, '<br><address>'); // 2. remove all tags but not linebreaks and address (<b>bla</b><br /> => bla<br />)
		$content = preg_replace('/\s+/', ' ', $content); // 3. removes tabs and whitespaces
		$content = $this->br2nl($content); // 4. <br /> to \n
		$content = implode("\n", t3lib_div::trimExplode("\n", $content)); // 5. explode and trim each line and implode again (" bla \n blabla " => "bla\nbla")
		$content = str_replace($notallowed, '', $content); // 6. remove not allowed signs

		return $content;
	}

	/**
	 * Function subpartsExists() checks if every part of the array contains min one sign
	 *
	 * @param	array		$array: Subpartarray
	 * @return	boolean
	 */
	public function subpartsExists($array) {
		if (count($array) > 0) { // if there are values
			foreach ($array as $key => $value) { // one loop for every array part
				if (!is_array($value)) { // first level
					if (strlen($value) == 0) return false; // error
				} else { // second level
					foreach ($value as $key2 => $value2) { // one loop for every array part in second level
						if (strlen($value2) == 0) return false; // error
					}
				}
			}
		}
		return true; // ok
	}

	/**
	 * Function TSmanipulation() manipulates session values before output with typoscript
	 * stdWrap for any value of the session
	 *
	 * @param	array		$array: Sessionvalues
	 * @param	string		$mode: Mode
	 * @param	array		$conf: TS setup configuration
	 * @param	array		$cObj: content object
	 * @return	boolean
	 */
	public function TSmanipulation($array, $mode, $conf, $cObj) {
		// config
		$this->conf = $conf;
		$this->cObj = $cObj;

		// let's go
		$arr = $this->arraytwo2arrayone($array);
			
		foreach ((array) $arr as $uid => $value) {
			if (!stristr($uid, 'uid')) { // ignore FILE
				continue;
			}
			
			// 1. check uid
			if (is_array($this->conf['mode.'][$mode . '.'][$uid . '.'])) { // if there are uid settings
				$tmp_arr = array_merge((array) $arr, (array) $this->cObj->data); // extend cObj
				$this->cObj->start($tmp_arr, 'tx_powermail_fields'); // enable .field in typoscript
				if (!stristr($uid, '_')) { // first level
					$array[$uid] = $this->cObj->cObjGetSingle($this->conf['mode.'][$mode . '.'][$uid], $this->conf['mode.'][$mode . '.'][$uid . '.']);
				} else { // second level (for checkboxes)
					$tmpuid = t3lib_div::trimExplode('_', $uid); // split uid at underscore
					$array[$tmpuid[0]][$tmpuid[1]] = $this->cObj->cObjGetSingle($this->conf['mode.'][$mode . '.'][$uid], $this->conf['mode.'][$mode . '.'][$uid . '.']);
				}
				continue; // go to the next loop (uid has priorty to type)
			}
			
			// 2. check type
			$type = $this->getTypeFromFieldUid($uid); // get field type
			if (is_array($this->conf['mode.'][$mode . '.'][$type . '.'])) { // if there are type settings
				$tmp_arr = array_merge((array) $this->cObj->data, array($type => $array[$uid])); // extend cObj
				$this->cObj->start($tmp_arr, 'tx_powermail_fields'); // enable .field in typoscript
				$array[$uid] = $this->cObj->cObjGetSingle($this->conf['mode.'][$mode . '.'][$type], $this->conf['mode.'][$mode . '.'][$type . '.']);
			}
		}

		return $array; // return array
	}

    /**
	 * Read type of a field
	 *
	 * @param	string		Field Uid
	 * @return	string		Label to field
	 */
    private function getTypeFromFieldUid($uid) {
		if (!stristr($uid, 'uid')) { // $name not like uid55
			return $uid;
		}
		
		$uid = str_replace('uid', '', $uid); // remove uid from uid43 to get 43
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'tx_powermail_fields.formtype',
			'
				tx_powermail_fields 
				LEFT JOIN tx_powermail_fieldsets ON tx_powermail_fields.fieldset = tx_powermail_fieldsets.uid 
				LEFT JOIN tt_content ON tt_content.uid = tx_powermail_fieldsets.tt_content
			',
			$where_clause = 'tx_powermail_fields.uid = ' . intval($uid),
			$groupBy = '',
			$orderBy = '',
			$limit = ''
		);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			return $row['formtype']; // return type
		}
    }

	/**
	 * Function arraytwo2arrayone() changes array with two levels to an array with one leven
	 * array('v1', array('v2')) => array('v1', 'v1_v2)
	 *
	 * @param	array		$array: Any array with values
	 * @return	array		$newarray
	 */
	public function arraytwo2arrayone($array) {
		$newarray = array();

		if (count($array) > 0 && is_array($array)) {
			foreach ($array as $k => $v) {
				if (!is_array($v)) { // first level

					$newarray[$k] = $v; // no change

				} else { // second level
					if (count($v) > 0) {

						foreach ($v as $k2 => $v2) {
							if (!is_array($v2)) $newarray[$k.'_'.$k2] = $v2; // change to first level
						}

					}
				}
			}
		}

		return $newarray;
	}

	/**
	 * Function parseFunc() parses a string to support LINK syntax
	 *
	 * @param	string		$str: Any string with maybe a link syntax
	 * @param	array		$cObj: content object
	 * @param	boolean		$act: (De)Activates whole function
	 * @return	array		$newarray
	 */
	public function parseFunc($str, $cObj, $act = 1) {
		if (!$act) { // if function should be turned off
			return $str; // just return given value
		}

		$this->cObj = $cObj;
		$parseFunc = $GLOBALS['TSFE']->tmpl->setup['lib.']['parseFunc_RTE.']; // Get parseFunc array from Globals
		$parseFunc['nonTypoTagStdWrap.']['encapsLines.']['removeWrapping'] = 1; // add removeWrapping to this array (we don't want the p-tags)

		return $this->cObj->parseFunc($str, $parseFunc); // return string
	}

	/**
	 * Function alternate() checks if a number is odd or even
	 *
	 * @param	integer		$int: Any number (maybe $i)
	 * @return	boolean
	 */
	public function alternate($int = 0) {
		if ($int % 2 != 0) { // odd or even
			return false; // return false
		} else {
			return true; // return true
		}
	}

	/**
	 * Function mimecheck() returns true or false if file fits mime check
	 *
	 * @param	string		$filename: Any filename (new.zip)
	 * @param	string		$origfilename: Any filename before transformation (original.zip)
	 * @return	boolean
	 */
	public function mimecheck($filename, $origfilename) {
		$ok = 0; // disallow on begin
		$ext = strtolower(array_pop(explode('.', $origfilename))); // get the extension of the upload

		$mime_types = array( // mime-type definition of files

			// basic mime-types
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => array (
				'application/zip',
				'application/x-compressed',
				'application/x-zip-compressed',
				'application/x-zip',
				'multipart/x-zip'
			),
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'docx' => array(
				'application/x-zip',
				'application/zip',
			),
			'xlsx' => array(
				'application/x-zip',
				'application/zip',
			),
			'pptx' => array(
				'application/x-zip',
				'application/zip',
			),

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
		);

			// If there is a mimetype definition of current uploaded file
		if (array_key_exists($ext, $mime_types)) {

				// 1. Get mimetype
				// Get mimetype via finfo (PECL-Extension needed)
			if (function_exists('finfo_open')) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $filename);
				$mimetype = array_shift(t3lib_div::trimExplode(';', $mimetype, 1));
				finfo_close($finfo);

				// Get mimetype via mime_content_type (Deprecated function, but sometimes still active)
			} elseif (function_exists('mime_content_type')) {
				$mimetype = mime_content_type($filename);

				// Use file-command with unix to determine
			} elseif (file_exists($this->confArr['fileToolPath'] . 'file')) {
				$mimetype = exec($this->confArr['fileToolPath'] . 'file -bi '. $filename);
				$mimetype = array_shift(t3lib_div::trimExplode(';', $mimetype, 1));

				// If no method above applies, shrug with your shoulders and make the result true
			} else {
				$ok = 1;
			}

			// 2. set variable $ok if ok
			if (!$ok) { // if it's not yet ok to upload files
				if (!is_array($mime_types[$ext])) { // if definition is not an array
					if ($mimetype == $mime_types[$ext]) { // if mimetype is correct
						$ok = 1; // allow upload
					}
				} else { // defintion is array
					if (in_array($mimetype, $mime_types[$ext])) { // if mimetype is in array
						$ok = 1; // allow upload
					}
				}
			}

			// 3. return 0/1
			if ($ok) { // if upload allowed
				return true; // upload allowed, stop further process
			} else { // if upload not allowed
				return false; // upload disallowed
			}

		} else { // no mime type definition in array above
			return true; // upload allowed
		}
	}

	/**
	 * Returns message with optical flair
	 *
	 * @param	string		$str: Message to show
	 * @param	int			$pos: Is this a positive message? (0,1,2)
	 * @param	boolean		$die: Process should be died now
	 * @param	boolean		$prefix: Activate or deactivate prefix "$extKey: "
	 * @param	string		$id: id to add to the message (maybe to do some javascript effects)
	 * @return	string		$string: Manipulated string
	 */
	public function msg($str, $pos = 0, $die = 0, $prefix = 1, $id = '') {
		// config
		if ($prefix) $string = $this->extKey . ($pos != 1 && $pos != 2 ? ' Error' : '') . ': ';  // Add prefix
		$string .= $str; // add string
		$URLprefix = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . '/'; // URLprefix with domain
		if (t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/' != t3lib_div::getIndpEnv('TYPO3_SITE_URL')) { // if request_host is different to site_url (TYPO3 runs in a subfolder)
		    $URLprefix .= str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/', '', t3lib_div::getIndpEnv('TYPO3_SITE_URL')); // add folder (like "subfolder/")
		}

		// let's go
		switch ($pos) {
			default: // error
				$wrap = '<div class="' . $this->extKey . '_msg_error" style="background-color: #FBB19B; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/error.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #DC4C42; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;

			case 1: // success
				$wrap = '<div class="' . $this->extKey . '_msg_status" style="background-color: #CDEACA; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/ok.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #58B548; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;

			case 2: // note
				$wrap = '<div class="' . $this->extKey . '_msg_error" style="background-color: #DDEEF9; background-position: 4px 4px; background-image: url(' . $URLprefix . 'typo3/gfx/information.png); background-repeat: no-repeat; padding: 5px 30px; font-weight: bold; border: 1px solid #8AAFC4; margin-bottom: 20px; font-family: arial, verdana; color: #444; font-size: 12px;"';
				if ($id) $wrap .= ' id="' . $id . '"'; // add css id
				$wrap .= '>';
				break;
		}

		if (!$die) {
			 return $wrap . $string . '</div>'; // return message
		} else {
			 die ($string); // die process and write message
		}
	}

	/**
	 * Construct for div class
	 *
	 * void
	 */
	public function __construct() {
		$this->confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_functions_div.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/powermail/lib/class.tx_powermail_functions_div.php']);
}
?>