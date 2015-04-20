<?php
namespace In2code\Powermail\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use In2code\Powermail\Utility\Div;

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
 * Mail Model
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Mail extends AbstractEntity {

	/**
	 * senderName
	 *
	 * @var \string
	 */
	protected $senderName = '';

	/**
	 * senderMail
	 *
	 * @var \string
	 */
	protected $senderMail = '';

	/**
	 * subject
	 *
	 * @var \string
	 */
	protected $subject = '';

	/**
	 * receiverMail
	 *
	 * @var \string
	 */
	protected $receiverMail = '';

	/**
	 * body
	 *
	 * @var \string
	 */
	protected $body = '';

	/**
	 * feuser
	 *
	 * @var \In2code\Powermail\Domain\Model\User
	 */
	protected $feuser = NULL;

	/**
	 * senderIp
	 *
	 * @var \string
	 */
	protected $senderIp = '';

	/**
	 * userAgent
	 *
	 * @var \string
	 */
	protected $userAgent = '';

	/**
	 * spamFactor
	 *
	 * @var \string
	 */
	protected $spamFactor = '';

	/**
	 * time
	 *
	 * @var int
	 */
	protected $time = NULL;

	/**
	 * form
	 *
	 * @var \In2code\Powermail\Domain\Model\Form
	 * @lazy
	 */
	protected $form = NULL;

	/**
	 * Powermail Answers
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Answer>
	 * @lazy
	 */
	protected $answers = NULL;

	/**
	 * crdate
	 *
	 * @var \DateTime
	 */
	protected $crdate = NULL;

	/**
	 * hidden
	 *
	 * @var \bool
	 */
	protected $hidden = FALSE;

	/**
	 * marketingRefererDomain
	 *
	 * @var \string
	 */
	protected $marketingRefererDomain = '';

	/**
	 * marketingReferer
	 *
	 * @var \string
	 */
	protected $marketingReferer = '';

	/**
	 * marketingCountry
	 *
	 * @var \string
	 */
	protected $marketingCountry = '';

	/**
	 * marketingMobileDevice
	 *
	 * @var \bool
	 */
	protected $marketingMobileDevice = FALSE;

	/**
	 * marketingFrontendLanguage
	 *
	 * @var \int
	 */
	protected $marketingFrontendLanguage = 0;

	/**
	 * marketingBrowserLanguage
	 *
	 * @var \string
	 */
	protected $marketingBrowserLanguage = '';

	/**
	 * marketingPageFunnel
	 *
	 * @var \string
	 */
	protected $marketingPageFunnel = '';

	/**
	 * __construct
	 */
	public function __construct() {
		$this->initStorageObjects();
	}

	/**
	 * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->answers = new ObjectStorage();
	}

	/**
	 * Returns the senderName
	 *
	 * @return string $senderName
	 */
	public function getSenderName() {
		return $this->senderName;
	}

	/**
	 * Sets the senderName
	 *
	 * @param string $senderName
	 * @return Mail
	 */
	public function setSenderName($senderName) {
		$this->senderName = $senderName;
		return $this;
	}

	/**
	 * Returns the senderMail
	 *
	 * @return string $senderMail
	 */
	public function getSenderMail() {
		return $this->senderMail;
	}

	/**
	 * Sets the senderMail
	 *
	 * @param string $senderMail
	 * @return Mail
	 */
	public function setSenderMail($senderMail) {
		$this->senderMail = $senderMail;
		return $this;
	}

	/**
	 * Returns the subject
	 *
	 * @return string $subject
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * Sets the subject
	 *
	 * @param string $subject
	 * @return Mail
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}

	/**
	 * Returns the receiverMail
	 *
	 * @return string $receiverMail
	 */
	public function getReceiverMail() {
		return $this->receiverMail;
	}

	/**
	 * Sets the receiverMail
	 *
	 * @param string $receiverMail
	 * @return Mail
	 */
	public function setReceiverMail($receiverMail) {
		$this->receiverMail = $receiverMail;
		return $this;
	}

	/**
	 * Returns the body
	 *
	 * @return string $body
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Sets the body
	 *
	 * @param string $body
	 * @return Mail
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}

	/**
	 * Returns the feuser
	 *
	 * @return \In2code\Powermail\Domain\Model\User $feuser
	 */
	public function getFeuser() {
		return $this->feuser;
	}

	/**
	 * Sets the feuser
	 *
	 * @param \In2code\Powermail\Domain\Model\User $feuser
	 * @return Mail
	 */
	public function setFeuser(User $feuser) {
		$this->feuser = $feuser;
		return $this;
	}

	/**
	 * Returns the spamFactor
	 *
	 * @return string $spamFactor
	 */
	public function getSpamFactor() {
		return $this->spamFactor;
	}

	/**
	 * Sets the spamFactor
	 *
	 * @param string $spamFactor
	 * @return Mail
	 */
	public function setSpamFactor($spamFactor) {
		$this->spamFactor = $spamFactor;
		return $this;
	}

	/**
	 * Returns the time
	 *
	 * @return int $time
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * Sets the time
	 *
	 * @param int $time
	 * @return Mail
	 */
	public function setTime($time) {
		$this->time = $time;
		return $this;
	}

	/**
	 * Returns the senderIp
	 *
	 * @return string $senderIp
	 */
	public function getSenderIp() {
		return $this->senderIp;
	}

	/**
	 * Sets the senderIp
	 *
	 * @param string $senderIp
	 * @return Mail
	 */
	public function setSenderIp($senderIp) {
		$this->senderIp = $senderIp;
		return $this;
	}

	/**
	 * Returns the userAgent
	 *
	 * @return string $userAgent
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}

	/**
	 * Sets the userAgent
	 *
	 * @param string $userAgent
	 * @return Mail
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
		return $this;
	}

	/**
	 * Returns the form
	 *
	 * @return \In2code\Powermail\Domain\Model\Form $form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Sets the form
	 *
	 * @param \In2code\Powermail\Domain\Model\Form $form
	 * @return Mail
	 */
	public function setForm(Form $form) {
		$this->form = $form;
		return $this;
	}

	/**
	 * Returns the answers
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public function getAnswers() {
		return $this->answers;
	}

	/**
	 * Sets the answers
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 * @return Mail
	 */
	public function setAnswers(ObjectStorage $answers) {
		$this->answers = $answers;
		return $this;
	}

	/**
	 * Adds an answer
	 *
	 * @param \In2code\Powermail\Domain\Model\Answer $answer
	 * @return void
	 */
	public function addAnswer(Answer $answer) {
		$this->answers->attach($answer);
	}

	/**
	 * Removes an answer
	 *
	 * @param \In2code\Powermail\Domain\Model\Answer $answerToRemove
	 * @return void
	 */
	public function removeAnswer(Answer $answerToRemove) {
		$this->answers->detach($answerToRemove);
	}

	/**
	 * Returns the crdate
	 *
	 * @return \DateTime $crdate
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * Sets the crdate
	 *
	 * @param \DateTime $crdate
	 * @return Mail
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
		return $this;
	}

	/**
	 * Returns the hidden
	 *
	 * @return bool $hidden
	 */
	public function getHidden() {
		return $this->hidden;
	}

	/**
	 * Sets the hidden
	 *
	 * @param bool $hidden
	 * @return Mail
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
		return $this;
	}

	/**
	 * @param string $marketingBrowserLanguage
	 * @return Mail
	 */
	public function setMarketingBrowserLanguage($marketingBrowserLanguage) {
		$this->marketingBrowserLanguage = $marketingBrowserLanguage;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMarketingBrowserLanguage() {
		return $this->marketingBrowserLanguage;
	}

	/**
	 * @param string $marketingCountry
	 * @return Mail
	 */
	public function setMarketingCountry($marketingCountry) {
		$this->marketingCountry = $marketingCountry;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMarketingCountry() {
		return $this->marketingCountry;
	}

	/**
	 * @param int $marketingFrontendLanguage
	 * @return Mail
	 */
	public function setMarketingFrontendLanguage($marketingFrontendLanguage) {
		$this->marketingFrontendLanguage = $marketingFrontendLanguage;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMarketingFrontendLanguage() {
		return $this->marketingFrontendLanguage;
	}

	/**
	 * @param boolean $marketingMobileDevice
	 * @return Mail
	 */
	public function setMarketingMobileDevice($marketingMobileDevice) {
		$this->marketingMobileDevice = $marketingMobileDevice;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getMarketingMobileDevice() {
		return $this->marketingMobileDevice;
	}

	/**
	 * @param array $marketingPageFunnel
	 * @return Mail
	 */
	public function setMarketingPageFunnel($marketingPageFunnel) {
		if (is_array($marketingPageFunnel)) {
			$marketingPageFunnel = json_encode($marketingPageFunnel);
		}
		$this->marketingPageFunnel = $marketingPageFunnel;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getMarketingPageFunnel() {
		if (Div::isJsonArray($this->marketingPageFunnel)) {
			return json_decode($this->marketingPageFunnel);
		}
		return (array) $this->marketingPageFunnel;
	}

	/**
	 * Returns the UID of the last page that the user has opened.
	 *
	 * @return int
	 */
	public function getMarketingPageFunnelLastPage() {
		$pageFunnel = $this->getMarketingPageFunnel();
		if (count($pageFunnel)) {
			return $pageFunnel[count($pageFunnel) - 1];
		}
		return 0;
	}

	/**
	 * Return marketing pagefunnel as commaseparated list
	 *
	 * @param string $glue
	 * @return string
	 */
	public function getMarketingPageFunnelString($glue = ', ') {
		$pageFunnel = $this->getMarketingPageFunnel();
		return implode($glue, $pageFunnel);
	}

	/**
	 * @param string $marketingReferer
	 * @return Mail
	 */
	public function setMarketingReferer($marketingReferer) {
		$this->marketingReferer = $marketingReferer;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMarketingReferer() {
		return $this->marketingReferer;
	}

	/**
	 * @param string $marketingRefererDomain
	 * @return Mail
	 */
	public function setMarketingRefererDomain($marketingRefererDomain) {
		$this->marketingRefererDomain = $marketingRefererDomain;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMarketingRefererDomain() {
		return $this->marketingRefererDomain;
	}

	/**
	 * @param int $pid
	 * @return Mail
	 */
	public function setPid($pid) {
		parent::setPid($pid);
		return $this;
	}
}