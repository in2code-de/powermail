<?php
namespace In2code\Powermail\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Utility\Div;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>
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
 * MailRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class MailRepository extends Repository {

	/**
	 * fieldRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Find all mails in given PID (BE List)
	 *
	 * @param int $pid
	 * @param array $settings TypoScript Config Array
	 * @param array $piVars Plugin Variables
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findAllInPid($pid = 0, $settings = array(), $piVars = array()) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);

		// initial filter
		$and = array(
			$query->equals('deleted', 0),
			$query->equals('pid', $pid)
		);

		// filter
		if (isset($piVars['filter'])) {
			foreach ((array) $piVars['filter'] as $field => $value) {

				// Standard Fields
				if (!is_array($value)) {
					// Fulltext Search
					if ($field === 'all' && !empty($value)) {
						$or = array(
							$query->like('sender_name', '%' . $value . '%'),
							$query->like('sender_mail', '%' . $value . '%'),
							$query->like('subject', '%' . $value . '%'),
							$query->like('receiver_mail', '%' . $value . '%'),
							$query->like('sender_ip', '%' . $value . '%'),
							$query->like('answers.value', '%' . $value . '%')
						);
						$and[] = $query->logicalOr($or);
					}

					// Form filter
					elseif ($field === 'form' && !empty($value)) {
						$and[] = $query->equals('form', $value);
					}

					// Time Filter Start
					elseif ($field === 'start' && !empty($value)) {
						$and[] = $query->greaterThan('crdate', strtotime($value));
					}

					// Time Filter Stop
					elseif ($field === 'stop' && !empty($value)) {
						$and[] = $query->lessThan('crdate', strtotime($value));
					}

					// Hidden Filter
					elseif ($field === 'hidden' && !empty($value)) {
						$and[] = $query->equals($field, ($value - 1));
					}

					// Other Fields
					elseif (!empty($value)) {
						$and[] = $query->like($field, '%' . $value . '%');
					}
				}

				// Answer Fields
				if (is_array($value)) {
					foreach ((array) $value as $answerField => $answerValue) {
						if (empty($answerValue) || $answerField === 'crdate') {
							continue;
						}
						$and[] = $query->equals('answers.field', $answerField);
						$and[] = $query->like('answers.value', '%' . $answerValue . '%');
					}
				}
			}
		}

		// create constraint
		$constraint = $query->logicalAnd($and);
		$query->matching($constraint);

		$query->setOrderings($this->getSorting($settings['sortby'], $settings['order'], $piVars));
		return $query->execute();
	}

	/**
	 * Find first mail in given PID
	 *
	 * @param int $pid
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findFirstInPid($pid = 0) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		$and = array(
			$query->equals('deleted', 0),
			$query->equals('pid', $pid)
		);
		$query->matching($query->logicalAnd($and));
		$query->setOrderings(array('crdate' => QueryInterface::ORDER_DESCENDING));
		$query->setLimit(1);
		$mails = $query->execute();
		return $mails->getFirst();
	}

	/**
	 * Find mails by given UID (also hidden and don't care about starting page)
	 *
	 * @param int $uid
	 * @return \In2code\Powermail\Domain\Model\Mail
	 */
	public function findByUid($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		$query->getQuerySettings()->setLanguageMode(NULL);

		$and = array(
			$query->equals('uid', $uid),
			$query->equals('deleted', 0)
		);
		$query->matching(
			$query->logicalAnd($and)
		);

		return $query->execute()->getFirst();
	}

	/**
	 * @param string $marker
	 * @param string $value
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @param int $pageUid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByMarkerValueForm($marker, $value, $form, $pageUid) {
		$query = $this->createQuery();
		$and = array(
			$query->equals('answers.field', $this->fieldRepository->findByMarkerAndForm($marker, $form->getUid())),
			$query->equals('answers.value', $value),
			$query->equals('pid', $pageUid)
		);
		$query->matching($query->logicalAnd($and));
		return $query->execute();
	}

	/**
	 * Query for Pi2
	 *
	 * @param \array $settings TypoScript Settings
	 * @param \array $piVars Plugin Variables
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findListBySettings($settings, $piVars) {
		$query = $this->createQuery();

		/**
		 * FILTER start
		 */
		$and = array(
			$query->greaterThan('uid', 0)
		);

		// FILTER: form
		if (intval($settings['main']['form']) > 0) {
			$and[] = $query->equals('form', $settings['main']['form']);
		}

		// FILTER: pid
		if (intval($settings['main']['pid']) > 0) {
			$and[] = $query->equals('pid', $settings['main']['pid']);
		}

		// FILTER: delta
		if (intval($settings['list']['delta']) > 0) {
			$and[] = $query->greaterThan('crdate', (time() - $settings['list']['delta']));
		}

		// FILTER: showownonly
		if ($settings['list']['showownonly']) {
			$and[] = $query->equals('feuser', $GLOBALS['TSFE']->fe_user->user['uid']);
		}

		// FILTER: abc
		if (isset($piVars['filter']['abc'])) {
			$and[] = $query->equals('answers.field', $settings['search']['abc']);
			$and[] = $query->like('answers.value', $piVars['filter']['abc'] . '%');
		}

		// FILTER: field
		if (isset($piVars['filter'])) {
			// fulltext
			$filter = array();
			if (!empty($piVars['filter']['_all'])) {
				$filter[] = $query->like('answers.value', '%' . $piVars['filter']['_all'] . '%');
			}

			// single field search
			foreach ((array) $piVars['filter'] as $field => $value) {
				if (is_numeric($field) && !empty($value)) {
					$filterAnd = array(
						$query->equals('answers.field', $field),
						$query->like('answers.value', '%' . $value . '%')
					);
					$filter[] = $query->logicalAnd($filterAnd);
				}
			}

			if (count($filter) > 0) {
				// switch between AND and OR
				if (
					!empty($settings['search']['logicalRelation']) && strtolower($settings['search']['logicalRelation']) === 'and'
				) {
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
		$query->setOrderings(array('crdate' => QueryInterface::ORDER_DESCENDING));

		// set limit
		if (intval($settings['list']['limit']) > 0) {
			$query->setLimit(intval($settings['list']['limit']));
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
	public function findGroupedFormUidsToGivenPageUid($pageUid = 0) {
		$queryResult = $this->findAllInPid($pageUid);
		$forms = array();
		foreach ($queryResult as $mail) {
			/** @var Form $form */
			$form = $mail->getForm();
			if ($form !== NULL) {
				if ((int) $form->getUid() > 0 && !in_array($form->getUid(), $forms)) {
					$forms[$form->getUid()] = $form->getTitle();
				}
			}
		}
		return $forms;
	}

	/**
	 * Find mails in UID List
	 *
	 * @param string $uidList Commaseparated UID List of mails
	 * @param array $sorting array('field' => 'asc')
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findByUidList($uidList, $sorting = array()) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		$and = array(
			$query->equals('deleted', 0),
			$query->in('uid', GeneralUtility::trimExplode(',', $uidList, TRUE))
		);
		$query->matching($query->logicalAnd($and));
		$query->setOrderings($this->getSorting('crdate', 'desc'));
		foreach ((array) $sorting as $field => $order) {
			if (empty($order)) {
				continue;
			}
			$query->setOrderings($this->getSorting($field, $order));
		}
		return $query->execute();
	}

	/**
	 * General settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		/** @var Typo3QuerySettings $querySettings */
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * return sorting array and respect
	 * settings and piVars
	 * 		return array(
	 * 			'property' => 'asc'
	 * 		)
	 *
	 * @param string $sortby
	 * @param string $order
	 * @param array $piVars
	 * @return array
	 */
	protected function getSorting($sortby, $order, $piVars = array()) {
		$sorting = array(
			$this->cleanStringForQuery(Div::conditionalVariable($sortby, 'crdate')) =>
				$this->getSortOrderByString($order)
		);
		if (!empty($piVars['sorting'])) {
			$sorting = array();
			foreach ((array) array_reverse($piVars['sorting']) as $property => $sortOrderName) {
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
	protected function getSortOrderByString($sortOrderString) {
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
	protected function cleanStringForQuery($string) {
		return preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
	}
}