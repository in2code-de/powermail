<?php
declare(strict_types = 1);
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class ReportingUtility
 */
class ReportingUtility
{

    /**
     * @var array
     */
    protected static $groupedProperties = [
        'marketingRefererDomain' => [],
        'marketingReferer' => [],
        'marketingCountry' => [],
        'marketingMobileDevice' => [],
        'marketingFrontendLanguage' => [],
        'marketingBrowserLanguage' => [],
        'marketingPageFunnelString' => [],
    ];

    /**
     * Get grouped mail answers for reporting
     *
     * @param QueryResultInterface|array $mails Mail array
     * @param int $limit Max number of allowed Labels
     * @param string $limitLabel Label for "Max Labels" - could be "all others"
     * @return array
     */
    public static function getGroupedAnswersFromMails(
        iterable $mails,
        int $limit = 5,
        string $limitLabel = 'All others'
    ): array {
        $groupedAnswers = [];
        foreach ($mails as $mail) {
            /** @var Mail $mail */
            foreach ($mail->getAnswers() as $answer) {
                /** @var Answer $answer */
                $value = $answer->getStringValue();
                if ($answer->getField() !== null) {
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
     * @param QueryResultInterface|array $mails Mails
     * @param int $limit Max Labels
     * @param string $limitLabel Label for "Max Labels" - could be "all others"
     * @return array
     * @throws PropertyNotAccessibleException
     */
    public static function getGroupedMarketingPropertiesFromMails(
        iterable $mails,
        int $limit = 10,
        string $limitLabel = 'All others'
    ): array {
        $groupedProperties = self::$groupedProperties;
        foreach ($mails as $mail) {
            /** @var Mail $mail */
            foreach (array_keys($groupedProperties) as $key) {
                $value = ObjectAccess::getProperty($mail, $key);
                if (!$value) {
                    $value = '-';
                }
                if (!isset($groupedProperties[$key][$value])) {
                    $groupedProperties[$key][$value] = 1;
                } else {
                    $groupedProperties[$key][$value]++;
                }
            }
        }
        self::sortReportingArrayDescending($groupedProperties);
        self::cutArrayByKeyLimitAndAddTotalValues($groupedProperties, $limit, $limitLabel);
        return $groupedProperties;
    }

    /**
     * Sort multiple array descending
     *
     * @param array $reportingArray
     * @return void
     */
    public static function sortReportingArrayDescending(array &$reportingArray): void
    {
        foreach (array_keys($reportingArray) as $key) {
            arsort($reportingArray[$key]);
        }
    }

    /**
     * Cut an array by the max allowed entries and add a total value
     *
     *        Example for a limit of 2:
     *        $before = array(
     *            array(
     *                'blue' => 5,
     *                'red' => 2,
     *                'yellow' => 9,
     *                'black' => 1
     *            )
     *        )
     *        $after = array(
     *            array(
     *                'blue' => 5,
     *                'red' => 2,
     *                'All others' => 10
     *            )
     *        )
     *
     * @param array $reportingArray
     * @param int $limit
     * @param string $limitLabel
     * @return void
     */
    public static function cutArrayByKeyLimitAndAddTotalValues(
        array &$reportingArray,
        int $limit,
        string $limitLabel
    ): void {
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
