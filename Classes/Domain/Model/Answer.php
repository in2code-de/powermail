<?php
namespace In2code\Powermail\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use In2code\Powermail\Utility\Div;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\Field;

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
 * Answer Model
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Answer extends AbstractEntity {

	/**
	 * value
	 *
	 * @var string
	 */
	protected $value = '';

	/**
	 * valueType
	 *      0 => text
	 *      1 => array
	 *      2 => date
	 *      3 => upload
	 *
	 * @var int
	 */
	protected $valueType = NULL;

	/**
	 * mail
	 *
	 * @var \In2code\Powermail\Domain\Model\Mail
	 */
	protected $mail = NULL;

	/**
	 * field
	 *
	 * @var \In2code\Powermail\Domain\Model\Field
	 */
	protected $field = NULL;

	/**
	 * Returns the value
	 *
	 * @return mixed $value
	 */
	public function getValue() {
		$value = $this->value;

			// if serialized, change to array
		if (Div::isJsonArray($this->value)) {
				// only if type multivalue or upload
			if ($this->getValueType() === 1 || $this->getValueType() === 3) {
				$value = json_decode($value, TRUE);
			}
		}

			// if type date
		if ($this->getValueType() === 2 && is_numeric($value) && $this->getField() !== NULL) {
			$value = date(
				LocalizationUtility::translate(
					'datepicker_format_' . $this->getField()->getDatepickerSettings(),
					'powermail'
				),
				$value
			);
		}

			// if multitext or upload force array
		if (($this->getValueType() === 1 || $this->getValueType() === 3) && !is_array($value)) {
			$value = (empty($value) ? array() : array(strval($value)));
		}

		return $value;
	}

	/**
	 * Sets the value
	 *
	 * @param mixed $value
	 * @dontvalidate $value
	 * @return void
	 */
	public function setValue($value) {
			// if array, encode to string
		if (is_array($value)) {
			$value = json_encode($value);
		}

			// if date, get timestamp (datepicker)
		if (
			!empty($value) &&
			method_exists($this->getField(), 'getType') &&
			$this->getValueType() === 2 &&
			!is_numeric($value)
		) {
			$format = LocalizationUtility::translate(
				'datepicker_format_' . $this->getField()->getDatepickerSettings(),
				'powermail'
			);
			$date = \DateTime::createFromFormat($format, $value);
			if ($date) {
				if ($this->getField()->getDatepickerSettings() === 'date') {
					$date->setTime(0, 0, 0);
				}
				$value = $date->getTimestamp();
			} else {
					// fallback html5 date field - always Y-m-d H:i
				$date = new \DateTime($value);
				if ($date) {
					if ($this->getField()->getDatepickerSettings() === 'date') {
						$date->setTime(0, 0, 0);
					}
					$value = $date->getTimestamp();
				}
			}
		}

		$this->value = $value;
	}

	/**
	 * Returns value and enforces a string
	 * 		An array will be returned as commaseparated string
	 *
	 * @param string $glue
	 * @return string
	 */
	public function getStringValue($glue = ', ') {
		$value = $this->getValue();
		if (is_array($value)) {
			$value = implode($glue, $value);
		}
		return (string) $value;
	}

	/**
	 * Returns raw value - could be
	 * 		- Same as getValue()
	 * 		- Timestamp (Date fields) instead of human readable date
	 * 		- JSON string for multiple fields instead of array
	 *
	 * @return string
	 */
	public function getRawValue() {
		return $this->value;
	}

	/**
	 * @param int $valueType
	 * @return void
	 */
	public function setValueType($valueType) {
		$this->valueType = intval($valueType);
	}

	/**
	 * @return int
	 */
	public function getValueType() {
		if ($this->valueType === NULL) {
			if ($this->getField() !== NULL) {
				$this->setValueType(Div::getDataTypeFromFieldType($this->getField()->getType()));
			} else {
				$this->setValue(0);
			}
		}
		return $this->valueType;
	}

	/**
	 * Returns the mail
	 *
	 * @return \In2code\Powermail\Domain\Model\Mail $mail
	 */
	public function getMail() {
		return $this->mail;
	}

	/**
	 * Sets the mail
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return void
	 */
	public function setMail(Mail $mail) {
		$this->mail = $mail;
	}

	/**
	 * Returns the field
	 *
	 * @return \In2code\Powermail\Domain\Model\Field $field
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * Sets the field
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return void
	 */
	public function setField(Field $field) {
		$this->field = $field;
	}

}