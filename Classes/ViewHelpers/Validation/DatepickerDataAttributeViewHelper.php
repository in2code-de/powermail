<?php
namespace In2code\Powermail\ViewHelpers\Validation;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use In2code\Powermail\Domain\Model\Field;

/**
 * Returns Data-Attributes for JS and Native Validation
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class DatepickerDataAttributeViewHelper extends AbstractValidationViewHelper {

	/**
	 * Returns Data Attribute Array Datepicker settings (FE + BE)
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @param \array $additionalAttributes To add further attributes
	 * @param \string $value of this field
	 * @return \array for data attributes
	 */
	public function render(Field $field = NULL, $additionalAttributes = array(), $value = '') {
		$additionalAttributes['data-datepicker-force'] =
			$this->settings['misc']['datepicker']['forceJavaScriptDatePicker'];
		$additionalAttributes['data-datepicker-settings'] = $this->getDatepickerSettings($field);
		$additionalAttributes['data-datepicker-months'] = $this->getMonthNames();
		$additionalAttributes['data-datepicker-days'] = $this->getDayNames();
		$additionalAttributes['data-datepicker-format'] = $this->getFormat($field);
		if ($value) {
			$additionalAttributes['data-date-value'] = $value;
		}

		$this->addMandatoryAttributes($additionalAttributes, $field);

		return $additionalAttributes;
	}

	/**
	 * Get Datepicker Settings
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return string
	 */
	protected function getDatepickerSettings(Field $field = NULL) {
		if ($field === NULL) {
			return 'datetime';
		}
		return $field->getDatepickerSettings();
	}

	/**
	 * Get timeformat out of datepicker type
	 *
	 * @param \In2code\Powermail\Domain\Model\Field $field
	 * @return string
	 */
	protected function getFormat(Field $field = NULL) {
		return LocalizationUtility::translate('datepicker_format_' . $this->getDatepickerSettings($field), $this->extensionName);
	}

	/**
	 * Generate Monthnames from locallang
	 *
	 * @return string
	 */
	protected function getDayNames() {
		$days = array(
			'so',
			'mo',
			'tu',
			'we',
			'th',
			'fr',
			'sa',
		);
		$dayArray = array();
		foreach ($days as $day) {
			$dayArray[] = LocalizationUtility::translate('datepicker_day_' . $day, $this->extensionName);
		}
		return implode(',', $dayArray);
	}

	/**
	 * Generate Monthnames from locallang
	 *
	 * @return string
	 */
	protected function getMonthNames() {
		$months = array(
			'jan',
			'feb',
			'mar',
			'apr',
			'may',
			'jun',
			'jul',
			'aug',
			'sep',
			'oct',
			'nov',
			'dec',
		);
		$monthArray = array();
		foreach ($months as $month) {
			$monthArray[] = LocalizationUtility::translate('datepicker_month_' . $month, $this->extensionName);
		}
		return implode(',', $monthArray);
	}
}