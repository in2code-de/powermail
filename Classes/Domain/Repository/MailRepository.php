<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Repository;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class MailRepository
 */
class MailRepository extends AbstractRepository
{
    use SignalTrait;

    /**
     * Find all mails in given PID (BE List)
     *
     * @param int $pid
     * @param array $settings TypoScript Config Array
     * @param array $piVars Plugin Variables
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findAllInPid($pid = 0, $settings = [], $piVars = [])
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $and = $this->getConstraintsForFindAllInPid($piVars, $query, $pid);
        $query->matching($query->logicalAnd($and));
        $query->setOrderings($this->getSorting($settings['sortby'], $settings['order'], $piVars));
        $mails = $query->execute();
        $mails = $this->makeUniqueQuery($mails, $query);
        return $mails;
    }

    /**
     * Workarround for "group by uid"
     *
     * @param QueryResultInterface $result
     * @param QueryInterface $query
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    protected function makeUniqueQuery(QueryResultInterface $result, QueryInterface $query)
    {
        if ($result->count() > 0) {
            $items = [];
            foreach ($result as $resultItem) {
                if (!in_array($resultItem->getUid(), $items)) {
                    $items[] = $resultItem->getUid();
                }
            }
            $query->matching($query->in('uid', $items));
            return $query->execute();
        }
        return $result;
    }

    /**
     * Find first mail in given PID
     *
     * @param int $pid
     * @return Mail
     */
    public function findFirstInPid($pid = 0)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $and = [
            $query->equals('deleted', 0),
            $query->equals('pid', $pid)
        ];
        $query->matching($query->logicalAnd($and));
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        $query->setLimit(1);
        $mails = $query->execute();
        $mail = $mails->getFirst();
        /** @var Mail $mail */
        return $mail;
    }

    /**
     * Find mails by given UID (also hidden and don't care about starting page)
     *
     * @param int $uid
     * @return Mail
     */
    public function findByUid($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setLanguageMode(null);

        $and = [
            $query->equals('uid', $uid),
            $query->equals('deleted', 0)
        ];
        $query->matching($query->logicalAnd($and));

        $mail = $query->execute()->getFirst();
        /** @var Mail $mail */
        return $mail;
    }

    /**
     * @param string $marker
     * @param string $value
     * @param Form $form
     * @param int $pageUid
     * @return QueryResultInterface
     */
    public function findByMarkerValueForm($marker, $value, $form, $pageUid)
    {
        $query = $this->createQuery();
        $fieldRepository = $this->objectManager->get(FieldRepository::class);
        $and = [
            $query->equals('answers.field', $fieldRepository->findByMarkerAndForm($marker, $form->getUid())),
            $query->equals('answers.value', $value),
            $query->equals('pid', $pageUid)
        ];
        $query->matching($query->logicalAnd($and));
        return $query->execute();
    }

    /**
     * Query for Pi2
     *
     * @param $settings
     * @param $piVars
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findListBySettings($settings, $piVars)
    {
        $query = $this->createQuery();

        /**
         * FILTER start
         */
        $and = [
            $query->greaterThan('uid', 0)
        ];

        // FILTER: form
        if ((int)$settings['main']['form'] > 0) {
            $and[] = $query->equals('form', $settings['main']['form']);
        }

        // FILTER: pid
        if ((int)$settings['main']['pid'] > 0) {
            $and[] = $query->equals('pid', $settings['main']['pid']);
        }

        // FILTER: delta
        if ((int)$settings['list']['delta'] > 0) {
            $and[] = $query->greaterThan('crdate', (time() - $settings['list']['delta']));
        }

        // FILTER: showownonly
        if ($settings['list']['showownonly']) {
            $and[] = $query->equals('feuser', FrontendUtility::getPropertyFromLoggedInFrontendUser());
        }

        // FILTER: abc
        if (isset($piVars['filter']['abc'])) {
            $and[] = $query->equals('answers.field', $settings['search']['abc']);
            $and[] = $query->like('answers.value', $piVars['filter']['abc'] . '%');
        }

        // FILTER: field
        if (isset($piVars['filter'])) {
            // fulltext
            $filter = [];
            if (!empty($piVars['filter']['_all'])) {
                $filter[] = $query->like('answers.value', '%' . $piVars['filter']['_all'] . '%');
            }

            // single field search
            foreach ((array)$piVars['filter'] as $field => $value) {
                if (is_numeric($field) && !empty($value)) {
                    $filterAnd = [
                        $query->equals('answers.field', $field),
                        $query->like('answers.value', '%' . $value . '%')
                    ];
                    $filter[] = $query->logicalAnd($filterAnd);
                }
            }

            if (count($filter) > 0) {
                // switch between AND and OR
                if (!empty($settings['search']['logicalRelation']) &&
                    strtolower($settings['search']['logicalRelation']) === 'and') {
                    $and[] = $query->logicalAnd($filter);
                } else {
                    $and[] = $query->logicalOr($filter);
                }
            }

        }

        // FILTER: create constraint
        $constraint = $query->logicalAnd($and);
        $query->matching($constraint);

        // sorting
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);

        // set limit
        if ((int)$settings['list']['limit'] > 0) {
            $query->setLimit((int)$settings['list']['limit']);
        }

        $mails = $query->execute();
        return $mails;
    }

    /**
     * Get all form uids from all mails stored on a given page
     *
     * @param int $pageUid
     * @return array
     */
    public function findGroupedFormUidsToGivenPageUid($pageUid = 0)
    {
        /** @var Query $query */
        $query = $this->createQuery();
        $tableName = $query->getSource()->getSelectorName();
        $sql = 'SELECT MIN(uid) uid, form FROM ' . $tableName
            . ' WHERE pid = ' . (int)$pageUid . ' AND deleted = 0 GROUP BY form';
        $query->statement($sql);
        $queryResult = $query->execute();

        $forms = [];
        foreach ($queryResult as $mail) {
            /** @var Form $form */
            $form = $mail->getForm();
            if ($form !== null) {
                if ((int)$form->getUid() > 0 && !in_array($form->getUid(), $forms)) {
                    $forms[$form->getUid()] = $form->getTitle();
                }
            }
        }
        $this->persistenceManager->clearState();
        return $forms;
    }

    /**
     * Find mails in UID List
     *
     * @param string $uidList Commaseparated UID List of mails
     * @param array $sorting array('field' => 'asc')
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByUidList($uidList, $sorting = [])
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $and = [
            $query->equals('deleted', 0),
            $query->in('uid', GeneralUtility::trimExplode(',', $uidList, true))
        ];
        $query->matching($query->logicalAnd($and));
        $query->setOrderings($this->getSorting('crdate', 'desc'));
        foreach ((array)$sorting as $field => $order) {
            if (empty($order)) {
                continue;
            }
            $query->setOrderings($this->getSorting($field, $order));
        }
        return $query->execute();
    }

    /**
     * Find the latest three mails by given form uid
     *
     * @param int $formUid
     * @param int $limit
     * @return QueryResultInterface
     */
    public function findLatestByForm($formUid, $limit = 3)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('form', $formUid));
        $query->setOrderings($this->getSorting('crdate', 'desc'));
        $query->setLimit($limit);
        return $query->execute();
    }

    /**
     * Generate a new array with labels
     *        label_firstname => Firstname
     *
     * @param Mail $mail
     * @return array
     */
    public function getLabelsWithMarkersFromMail(Mail $mail)
    {
        $variables = [];
        foreach ($mail->getAnswers() as $answer) {
            if (method_exists($answer, 'getField') && method_exists($answer->getField(), 'getMarker')) {
                $variables['label_' . $answer->getField()->getMarker()] = $answer->getField()->getTitle();
            }
        }
        return $variables;
    }

    /**
     * Generate a new array with markers and their values
     *        firstname => value
     *
     * @param Mail $mail
     * @param bool $htmlSpecialChars
     * @return array
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getVariablesWithMarkersFromMail(Mail $mail, $htmlSpecialChars = false)
    {
        $variables = [];
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
        if ($htmlSpecialChars) {
            $variables = ArrayUtility::htmlspecialcharsOnArray($variables);
        }

        $signalArguments = [&$variables, $mail, $this];
        $this->signalDispatch(__CLASS__, __FUNCTION__, $signalArguments);
        return $variables;
    }

    /**
     * Returns senderemail from a couple of arguments
     *
     * @param Mail $mail
     * @param string $default
     * @return string Sender Email
     */
    public function getSenderMailFromArguments(Mail $mail, $default = null)
    {
        $email = '';
        foreach ($mail->getAnswers() as $answer) {
            if ($answer->getField() !== null &&
                $answer->getField()->isSenderEmail() &&
                GeneralUtility::validEmail(trim($answer->getValue()))
            ) {
                $email = trim($answer->getValue());
                break;
            }
        }
        if (empty($email)) {
            $email = $this->getSenderMailFromDefault($default);
        }
        return $email;
    }

    /**
     * Returns sendername from a couple of arguments
     *
     * @param Mail $mail Given Params
     * @param string|array $default String as default or cObject array
     * @param string $glue
     * @return string Sender Name
     */
    public function getSenderNameFromArguments(Mail $mail, $default = null, $glue = ' ')
    {
        $name = '';
        foreach ($mail->getAnswers() as $answer) {
            /** @var Answer $answer */
            if (method_exists($answer->getField(), 'getUid') && $answer->getField()->isSenderName()) {
                if (!is_array($answer->getValue())) {
                    $value = $answer->getValue();
                } else {
                    $value = implode($glue, $answer->getValue());
                }
                $name .= $value . $glue;
            }
        }

        if (!trim($name) && $default) {
            if (!is_array($default)) {
                $name = $default;
            } else {
                /** @var ContentObjectRenderer $contentObject */
                $contentObject = ObjectUtility::getObjectManager()->get(ContentObjectRenderer::class);
                $name = $contentObject->cObjGetSingle($default[0][$default[1]], $default[0][$default[1] . '.']);
            }
        }

        if (empty($name) && !empty(ConfigurationUtility::getDefaultMailFromName())) {
            $name = ConfigurationUtility::getDefaultMailFromName();
        }

        if (!trim($name)) {
            $name = LocalizationUtility::translate('error_no_sender_name');
        }
        return trim($name);
    }

    /**
     * return sorting array and respect
     * settings and piVars
     *        return array(
     *            'property' => 'asc'
     *        )
     *
     * @param string $sortby
     * @param string $order
     * @param array $piVars
     * @return array
     */
    protected function getSorting($sortby, $order, $piVars = [])
    {
        $sorting = [
            $this->cleanStringForQuery(StringUtility::conditionalVariable($sortby, 'crdate')) =>
                $this->getSortOrderByString($order)
        ];
        if (!empty($piVars['sorting'])) {
            $sorting = [];
            foreach ((array)array_reverse($piVars['sorting']) as $property => $sortOrderName) {
                $sorting[$this->cleanStringForQuery($property)] = $this->getSortOrderByString($sortOrderName);
            }
        }
        return $sorting;
    }

    /**
     * Get sort order (ascending or descending) by given string
     *
     * @param string $sortOrderString
     * @return string
     */
    protected function getSortOrderByString($sortOrderString)
    {
        $sortOrder = QueryInterface::ORDER_ASCENDING;
        if ($sortOrderString !== 'asc') {
            $sortOrder = QueryInterface::ORDER_DESCENDING;
        }
        return $sortOrder;
    }

    /**
     * Make in impossible to hack a sql string
     * if we just remove as much unneeded characters
     * as possible
     *
     * @param string $string
     * @return string
     */
    protected function cleanStringForQuery($string)
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
    }

    /**
     * General settings
     *
     * @return void
     */
    public function initializeObject()
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Get sender default email address
     *
     * @param string $default
     * @return string
     */
    protected function getSenderMailFromDefault($default)
    {
        $email = LocalizationUtility::translate('error_no_sender_email') . '@';
        $email .= str_replace('www.', '', GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'));
        if (GeneralUtility::validEmail(ConfigurationUtility::getDefaultMailFromAddress())) {
            $email = ConfigurationUtility::getDefaultMailFromAddress();
        }
        if (!empty($default)) {
            $email = $default;
        }
        return $email;
    }

    /**
     * @param array $piVars
     * @param QueryInterface $query
     * @param $pid
     * @return array
     * @throws InvalidQueryException
     */
    protected function getConstraintsForFindAllInPid(array $piVars, QueryInterface $query, $pid)
    {
        $and = [
            $query->equals('deleted', 0),
            $query->equals('pid', $pid)
        ];
        if (isset($piVars['filter'])) {
            foreach ((array)$piVars['filter'] as $field => $value) {
                if (!is_array($value)) {
                    if ($field === 'all' && !empty($value)) {
                        $or = [
                            $query->like('sender_name', '%' . $value . '%'),
                            $query->like('sender_mail', '%' . $value . '%'),
                            $query->like('subject', '%' . $value . '%'),
                            $query->like('receiver_mail', '%' . $value . '%'),
                            $query->like('sender_ip', '%' . $value . '%'),
                            $query->like('answers.value', '%' . $value . '%')
                        ];
                        $and[] = $query->logicalOr($or);
                    } elseif ($field === 'form' && !empty($value)) {
                        $and[] = $query->equals('form', $value);
                    } elseif ($field === 'start' && !empty($value)) {
                        $and[] = $query->greaterThan('crdate', strtotime($value));
                    } elseif ($field === 'stop' && !empty($value)) {
                        $and[] = $query->lessThan('crdate', strtotime($value));
                    } elseif ($field === 'hidden' && !empty($value)) {
                        $and[] = $query->equals($field, ($value - 1));
                    } elseif (!empty($value)) {
                        $and[] = $query->like($field, '%' . $value . '%');
                    }
                }

                // Answer Fields
                if (is_array($value)) {
                    foreach ((array)$value as $answerField => $answerValue) {
                        if (!empty($answerValue) && $answerField !== 'crdate') {
                            $and[] = $query->equals('answers.field', $answerField);
                            $and[] = $query->like('answers.value', '%' . $answerValue . '%');
                        }
                    }
                }
            }
        }
        return $and;
    }

    /**
     * @param int $mailIdentifier
     * @return void
     */
    public function removeFromDatabase(int $mailIdentifier)
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Mail::TABLE_NAME);
        $queryBuilder->delete(Mail::TABLE_NAME)->where('uid=' . (int)$mailIdentifier)->execute();
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Answer::TABLE_NAME);
        $queryBuilder->delete(Answer::TABLE_NAME)->where('mail=' . (int)$mailIdentifier)->execute();
    }
}
