<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Reporting utility functions
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Reporting {

	/**
	 * @var array
	 */
	protected static $groupedMarketingProperties = array(
		'marketingRefererDomain' => array(),
		'marketingReferer' => array(),
		'marketingCountry' => array(),
		'marketingMobileDevice' => array(),
		'marketingFrontendLanguage' => array(),
		'marketingBrowserLanguage' => array(),
		'marketingPageFunnelString' => array(),
	);

	/**
	 * Get grouped mail answers for reporting
	 *
	 * @param QueryResult $mails Mail array
	 * @param int $limit Max number of allowed Labels
	 * @param string $limitLabel Label for "Max Labels" - could be "all others"
	 * @return array
	 */
	public static function getGroupedAnswersFromMails($mails, $limit = 5, $limitLabel = 'All others') {
		$groupedAnswers = array();
		foreach ($mails as $mail) {
			/** @var Mail $mail */
			foreach ($mail->getAnswers() as $answer) {
				/** @var Answer $answer */
				$value = $answer->getStringValue();
				if ($answer->getField() !== NULL) {
					$uid = $answer->getField()->getUid();
					if (!isset($groupedAnswers[$uid][$value])) {
						$groupedAnswers[$uid][$value] = 1;
					} else {
						$groupedAnswers[$uid][$value]++;
					}
				}
			}
		}
		self::sortReportingArrayDescending($groupedAnswers);
		self::cutArrayByKeyLimitAndAddTotalValues($groupedAnswers, $limit, $limitLabel);
		return $groupedAnswers;
	}

	/**
	 * Get grouped marketing stuff for reporting
	 *
	 * @param QueryResult $mails Mails
	 * @param int $limit Max Labels
	 * @param string $limitLabel Label for "Max Labels" - could be "all others"
	 * @return array
	 */
	public static function getGroupedMarketingPropertiesFromMails($mails, $limit = 10, $limitLabel = 'All others') {
		$groupedMarketingProperties = self::$groupedMarketingProperties;
		foreach ($mails as $mail) {
			/** @var Mail $mail */
			foreach (array_keys($groupedMarketingProperties) as $key) {
				$value = ObjectAccess::getProperty($mail, $key);
				if (!$value) {
					$value = '-';
				}
				if (!isset($groupedMarketingProperties[$key][$value])) {
					$groupedMarketingProperties[$key][$value] = 1;
				} else {
					$groupedMarketingProperties[$key][$value]++;
				}
			}
		}
		self::sortReportingArrayDescending($groupedMarketingProperties);
		self::cutArrayByKeyLimitAndAddTotalValues($groupedMarketingProperties, $limit, $limitLabel);
		return $groupedMarketingProperties;
	}

	/**
	 * Sort multiple array descending
	 *
	 * @param array $reportingArray
	 * @return void
	 */
	public static function sortReportingArrayDescending(&$reportingArray) {
		foreach (array_keys($reportingArray) as $key) {
			arsort($reportingArray[$key]);
		}
	}

	/**
	 * Cut an array by the max allowed entries and add a total value
	 *
	 * 		Example for a limit of 2:
	 * 		$before = array(
	 * 			array(
	 * 				'blue' => 5,
	 * 				'red' => 2,
	 * 				'yellow' => 9,
	 * 				'black' => 1
	 * 			)
	 * 		)
	 * 		$after = array(
	 * 			array(
	 * 				'blue' => 5,
	 * 				'red' => 2,
	 * 				'All others' => 10
	 * 			)
	 * 		)
	 *
	 * @param array $reportingArray
	 * @param int $limit
	 * @param string $limitLabel
	 * @return void
	 */
	public static function cutArrayByKeyLimitAndAddTotalValues(&$reportingArray, $limit, $limitLabel) {
		foreach (array_keys($reportingArray) as $key) {
			if (count($reportingArray[$key]) >= $limit) {
				$i = $totalAmount = 0;
				foreach ($reportingArray[$key] as $value => $amount) {
					$i++;
					if ($i >= $limit) {
						unset($reportingArray[$key][$value]);
						$totalAmount += $amount;
					}
				}
				$reportingArray[$key][$limitLabel] = $totalAmount;
			}
		}
	}
}