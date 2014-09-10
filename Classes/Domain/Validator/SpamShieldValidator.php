<?php
namespace In2code\Powermail\Domain\Validator;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \In2code\Powermail\Utility\Div;

/**
 * SpamShieldValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SpamShieldValidator extends \In2code\Powermail\Domain\Validator\AbstractValidator {

	/**
	 * Spam indication start value
	 *
	 * @var integer
	 */
	protected $spamIndicator = 0;

	/**
	 * TypoScript Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Referrer action
	 *
	 * @var string
	 */
	protected $referrer;

	/**
	 * Plugin arguments
	 *
	 * @var array
	 */
	protected $piVars;

	/**
	 * Error messages for email to admin
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Spam-Validation of given Params
	 * 		see powermail/doc/SpamDetection for formula
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		if (!$this->settings['spamshield.']['_enable']) {
			return $this->getIsValid();
		}
		$spamFactor = $this->settings['spamshield.']['factor'] / 100;

		// Different checks to increase spam indicator
		$this->honeypodCheck($this->settings['spamshield.']['indicator.']['honeypod']);
		$this->linkCheck(
			$mail, $this->settings['spamshield.']['indicator.']['link'],
			$this->settings['spamshield.']['indicator.']['linkLimit']
		);
		$this->nameCheck($mail, $this->settings['spamshield.']['indicator.']['name']);
		$this->sessionCheck($mail, $this->settings['spamshield.']['indicator.']['session']);
		$this->uniqueCheck($mail, $this->settings['spamshield.']['indicator.']['unique']);
		$this->blacklistStringCheck($mail, $this->settings['spamshield.']['indicator.']['blacklistString']);
		$this->blacklistIpCheck($this->settings['spamshield.']['indicator.']['blacklistIp']);

		// spam formula with asymptote 1 (100%)
		if ($this->spamIndicator > 0) {
			$thisSpamFactor = -1 / $this->spamIndicator + 1;
		} else {
			$thisSpamFactor = 0;
		}

		// Save Spam Factor in session for db storage
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail_spamfactor', $this->formatSpamFactor($thisSpamFactor));
		$GLOBALS['TSFE']->storeSessionData();

		// Spam debugging
		if ($this->settings['debug.']['spamshield']) {
			GeneralUtility::devLog(
				'Spamshield (Spamfactor ' . $this->formatSpamFactor($thisSpamFactor) . ')',
				'powermail',
				0,
				$this->getMessages()
			);
		}

		// if spam
		if ($thisSpamFactor >= $spamFactor) {
			$this->addError('spam_details', $this->formatSpamFactor($thisSpamFactor));
			$this->setIsValid(FALSE);

			// Send notification email to admin
			if (GeneralUtility::validEmail($this->settings['spamshield.']['email'])) {
				$subject = 'Spam in powermail form recognized';
				$message = 'Possible spam in powermail form on page with PID ' . $GLOBALS['TSFE']->id;
				$message .= "\n\n";
				$message .= 'Spamfactor of this mail: ' . $this->formatSpamFactor($thisSpamFactor) . "\n";
				$message .= "\n\n";
				$message .= 'Failed Spamchecks:' . "\n";
				$message .= Div::viewPlainArray($this->getMessages());
				$message .= "\n\n";
				$message .= 'Given Form variables:' . "\n";
				foreach ($mail->getAnswers() as $answer) {
					$message .= $answer->getField()->getTitle();
					$message .= ': ';
					$message .= $answer->getValue();
					$message .= "\n";
				}
				$header  = 'MIME-Version: 1.0' . "\r\n";
				$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$header .= 'From: powermail@' . GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY') . "\r\n";
				GeneralUtility::plainMailEncoded(
					$this->settings['spamshield.']['email'],
					$subject,
					$message,
					$header
				);
			}

		}

		return $this->getIsValid();
	}

	/**
	 * Honeypod Check: Spam recognized if Honeypod field is filled
	 *
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function honeypodCheck($indication = 1.0) {
		if (!$indication) {
			return;
		}

		// if honeypod was filled
		if (!empty($this->piVars['field']['__hp'])) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
		}
	}

	/**
	 * Link Check: Counts numbers of links in message
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param float $indication Indication if check fails
	 * @param integer $limit Limit of allowed links in mail
	 * @return void
	 */
	protected function linkCheck(\In2code\Powermail\Domain\Model\Mail $mail, $indication = 1.0, $limit = 2) {
		if (!$indication) {
			return;
		}

		// check numbers of links
		$linkAmount = 0;
		foreach ($mail->getAnswers() as $answer) {
			if (is_array($answer->getValue())) {
				continue;
			}
			preg_match_all('@http://|https://|ftp.@', $answer->getValue(), $result);
			if (isset($result[0])) {
				// add numbers of http://
				$linkAmount += count($result[0]);
			}
		}

		// check if number of failes are too high
		if ($linkAmount > $limit) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
		}
	}

	/**
	 * Name Check: Compares first- and lastname (shouldn't be the same)
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function nameCheck(\In2code\Powermail\Domain\Model\Mail $mail, $indication = 1.0) {
		if (!$indication) {
			return;
		}
		$keysFirstName = array(
			'first_name',
			'firstname',
			'vorname'
		);
		$keysLastName = array(
			'last_name',
			'lastname',
			'sur_name',
			'surname',
			'nachname',
			'name'
		);

		// find out first- and lastname (marker should be {firstname} and {lastname}
		foreach ($mail->getAnswers() as $answer) {
			if (is_array($answer->getValue())) {
				continue;
			}
			if (in_array($answer->getField()->getMarker(), $keysFirstName)) {
				$firstname = $answer->getValue();
			}
			if (in_array($answer->getField()->getMarker(), $keysLastName)) {
				$lastname = $answer->getValue();
			}
		}

		// if check failes
		if (isset($firstname) && isset($lastname)) {
			if ($firstname && $firstname == $lastname) {
				$this->increaseSpamIndicator($indication);
				$this->addMessage(__FUNCTION__ . ' failed');
				return;
			}
		}
	}

	/**
	 * Session Check: Checks if session was started correct on form delivery
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function sessionCheck(\In2code\Powermail\Domain\Model\Mail $mail, $indication = 1.0) {
		// Stop if indicator was turned to 0 OR if last action was optinConfirm
		if (!$indication || $this->referrer == 'optinConfirm') {
			return;
		}
		$time = Div::getFormStartFromSession($mail->getForm()->getUid());

		// if check failes
		if (!isset($time) || !$time) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
		}
	}

	/**
	 * Unique Check: Checks if values in given params are different
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function uniqueCheck(\In2code\Powermail\Domain\Model\Mail $mail, $indication = 1.0) {
		if (!$indication) {
			return;
		}

		$arr = array();
		foreach ($mail->getAnswers() as $answer) {

			// don't want values in second level (from checkboxes e.g.)
			if (is_array($answer->getValue())) {
				continue;
			}

			if ($answer->getValue()) {
				$arr[] = $answer->getValue();
			}
		}

		// if check failes
		if (count($arr) != count(array_unique($arr))) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
			return;
		}
	}

	/**
	 * Blacklist String Check: Check if a blacklisted word is in given values
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function blacklistStringCheck(\In2code\Powermail\Domain\Model\Mail $mail, $indication = 1.0) {
		if (!$indication) {
			return;
		}
		$blacklist = GeneralUtility::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistStringValues'], TRUE);

		// if check failes
		foreach ($mail->getAnswers() as $answer) {
			if (is_array($answer->getValue())) {
				continue;
			}
			foreach ((array) $blacklist as $blackword) {
				if (stristr($answer->getValue(), $blackword)) {
					$this->increaseSpamIndicator($indication);
					$this->addMessage(__FUNCTION__ . ' failed');
					return;
				}
			}
		}
	}

	/**
	 * Blacklist IP-Address Check: Check if Senders IP is blacklisted
	 *
	 * @param float $indication Indication if check fails
	 * @return void
	 */
	protected function blacklistIpCheck($indication = 1.0) {
		if (!$indication) {
			return;
		}
		$blacklist = GeneralUtility::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistIpValues'], TRUE);

		// if check failes
		if (in_array(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $blacklist)) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
			return;
		}
	}

	/**
	 * Format for Spamfactor (0.23 => 23%)
	 *
	 * @param float $factor
	 * @return float
	 */
	protected function formatSpamFactor($factor) {
		return number_format(($factor * 100), 0) . '%';
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->piVars = GeneralUtility::_GP('tx_powermail_pi1');
		$this->referrer = $this->piVars['__referrer']['@action'];
	}

	/**
	 * @param int $spamIndicator
	 * @return void
	 */
	public function setSpamIndicator($spamIndicator) {
		$this->spamIndicator = $spamIndicator;
	}

	/**
	 * @return int
	 */
	public function getSpamIndicator() {
		return $this->spamIndicator;
	}

	/**
	 * Increase Global Indicator
	 *
	 * @param int $indication
	 * @return void
	 */
	public function increaseSpamIndicator($indication) {
		$this->setSpamIndicator($this->getSpamIndicator() + $indication);
	}

	/**
	 * @param array $messages
	 * @return void
	 */
	public function setMessages($messages) {
		$this->messages = $messages;
	}

	/**
	 * @return array
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * Add $message
	 *
	 * @param $message
	 * @return void
	 */
	public function addMessage($message) {
		$messages = $this->getMessages();
		$messages[] = $message;
		$this->setMessages($messages);
	}

}