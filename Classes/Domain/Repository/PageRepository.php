<?php
namespace In2code\Powermail\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 in2code GmbH <info@in2code.de>, in2code.de
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
 * PageRepository
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class PageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Get title from table "pages" in TYPO3
	 *
	 * @param int $uid
	 * @return string
	 */
	public function getPageNameFromUid($uid) {
		$query = $this->createQuery();

		$sql = 'select title';
		$sql .= ' from pages';
		$sql .= ' where uid = ' . intval($uid);
		$sql .= ' limit 1';

		$result = $query->statement($sql)->execute(TRUE);

		return $result[0]['title'];
	}

	/**
	 * Get all pages with tt_content with a Powermail Plugin
	 *
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @return array
	 */
	public function getPagesWithContentRelatedToForm($form) {
		$query = $this->createQuery();

		$searchString = '%<field index=\"settings.flexform.main.form\">';
		$searchString .= '\n                    <value index=\"vDEF\">' . $form->getUid() . '</value>%';
		$sql = 'select distinct pages.title, pages.uid';
		$sql .= ' from pages left join tt_content on tt_content.pid = pages.uid';
		$sql .= ' where tt_content.list_type = "powermail_pi1"';
		$sql .= ' and tt_content.deleted = 0 and pages.deleted = 0';
		$sql .= ' and tt_content.pi_flexform like "' . $searchString . '"';

		$result = $query->statement($sql)->execute(TRUE);
		return $result;
	}
}