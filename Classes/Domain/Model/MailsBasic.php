<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Context Model for Tx_Powermail_Domain_Model_Mails without relations
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Powermail_Domain_Model_MailsBasic extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * senderName
	 *
	 * @var string
	 */
	protected $senderName = '';

	/**
	 * senderMail
	 *
	 * @var string
	 */
	protected $senderMail = '';

	/**
	 * subject
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * receiverMail
	 *
	 * @var string
	 */
	protected $receiverMail = '';

	/**
	 * body
	 *
	 * @var string
	 */
	protected $body = '';

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param string $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string $receiverMail
	 */
	public function setReceiverMail($receiverMail) {
		$this->receiverMail = $receiverMail;
	}

	/**
	 * @return string
	 */
	public function getReceiverMail() {
		return $this->receiverMail;
	}

	/**
	 * @param string $senderMail
	 */
	public function setSenderMail($senderMail) {
		$this->senderMail = $senderMail;
	}

	/**
	 * @return string
	 */
	public function getSenderMail() {
		return $this->senderMail;
	}

	/**
	 * @param string $senderName
	 */
	public function setSenderName($senderName) {
		$this->senderName = $senderName;
	}

	/**
	 * @return string
	 */
	public function getSenderName() {
		return $this->senderName;
	}

}
?>