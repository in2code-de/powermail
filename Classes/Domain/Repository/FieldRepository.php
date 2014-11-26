<?php
namespace In2code\Powermail\Domain\Repository;

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
 * FieldRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class FieldRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * formRepository
	 *
	 * @var \In2code\Powermail\Domain\Repository\FormRepository
	 * @inject
	 */
	protected $formRepository;

	/**
	 * @param \array $uids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUids($uids) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching(
			$query->in('uid', $uids)
		);
		return $query->execute();
	}

	/**
	 * Return uid from given field marker and form
	 *
	 * @param \string $marker
	 * @param \int $formUid
	 * @return \In2code\Powermail\Domain\Model\Field
	 */
	public function findByMarkerAndForm($marker, $formUid = 0) {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		if ($confArr['replaceIrreWithElementBrowser']) {
			return $this->findByMarkerAndFormAlternative($marker, $formUid);
		}

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$query->matching(
			$query->logicalAnd(
				array(
					$query->equals('marker', $marker),
					$query->equals('pages.forms.uid', $formUid)
				)
			)
		);
		$query->setLimit(1);
		$result = $query->execute()->getFirst();
		return $result;
	}

	/**
	 * Find all localized records with
	 * 		tx_powermail_domain_model_fields.marker != ""
	 *
	 * @return mixed
	 */
	public function findAllFieldsWithFilledMarkerrsInLocalizedFields() {
		$query = $this->createQuery();

		$sql = 'select uid,pid,title,marker,sys_language_uid';
		$sql .= ' from tx_powermail_domain_model_fields';
		$sql .= ' where marker != ""';
		$sql .= ' and sys_language_uid > 0';
		$sql .= ' and deleted = 0';
		$sql .= ' limit 1';

		$result = $query->statement($sql)->execute(TRUE);

		return $result;
	}

	/**
	 * Fix wrong localized fields with markers
	 *
	 * @return void
	 */
	public function fixFilledMarkersInLocalizedFields() {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_powermail_domain_model_fields',
			'sys_language_uid > 0 and deleted = 0 and marker != ""',
			array('marker' => '')
		);
	}

	/**
	 * Return uid from given field marker and form (if no IRRE)
	 *
	 * @param \string $marker
	 * @param \int $formUid
	 * @return \In2code\Powermail\Domain\Model\Field
	 */
	protected function findByMarkerAndFormAlternative($marker, $formUid = 0) {
		// get pages from form
		$form = $this->formRepository->findByUid($formUid);
		$pageUids = array();
		foreach ($form->getPages() as $page) {
			$pageUids[] = $page->getUid();
		}

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$query->matching(
			$query->logicalAnd(
				array(
					$query->equals('marker', $marker),
					$query->in('pages', $pageUids)
				)
			)
		);
		return $query
			->setLimit(1)
			->execute()
			->getFirst();
	}
}