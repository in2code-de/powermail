<?php
namespace In2code\Powermail\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use In2code\Powermail\Utility\Configuration;
use In2code\Powermail\Utility\Div;
use In2code\Powermail\Domain\Model\Mail;

/**
 * SpamShieldValidator
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class SpamShieldValidator extends AbstractValidator {

	/**
	 * Spam indication
	 *
	 * @var integer
	 */
	protected $spamIndicator = 0;

	/**
	 * Spam tolerance limit
	 *
	 * @var float
	 */
	protected $spamFactorLimit = 1.0;

	/**
	 * Calculated spam factor
	 *
	 * @var float
	 */
	protected $calculatedMailSpamFactor = 0.0;

	/**
	 * Referrer action
	 *
	 * @var string
	 */
	protected $referrer;

	/**
	 * Error messages for email to admin
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $typoScriptFrontendController;

	/**
	 * TypoScript Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Plugin arguments
	 *
	 * @var array
	 */
	protected $piVars;

	/**
	 * @var array
	 */
	protected $configurationArray;

	/**
	 * Spam-Validation of given Params
	 * 		see powermail/doc/SpamDetection for formula
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return bool
	 */
	public function isValid($mail) {
		if (empty($this->settings['spamshield.']['_enable'])) {
			return $this->getIsValid();
		}
		$this->runSpamPreventationMethods($mail);
		$this->calculateMailSpamFactor();
		$this->saveSpamFactorInSession();
		$this->saveSpamPropertiesInDevelopmentLog();

		if ($this->isSpamToleranceLimitReached()) {
			$this->addError('spam_details', $this->getCalculatedMailSpamFactor(TRUE));
			$this->setIsValid(FALSE);
			$this->sendSpamNotificationMail($mail, $this->getCalculatedMailSpamFactor());
		}

		return $this->getIsValid();
	}

	/**
	 * Start different checks to increase spam indicator
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return void
	 */
	protected function runSpamPreventationMethods($mail) {
		$settingsSpamShieldIndicator = $this->settings['spamshield.']['indicator.'];
		$this->honeypodCheck($settingsSpamShieldIndicator['honeypod']);
		$this->linkCheck($mail, $settingsSpamShieldIndicator['link'], $settingsSpamShieldIndicator['linkLimit']);
		$this->nameCheck($mail, $settingsSpamShieldIndicator['name']);
		$this->sessionCheck(
			$settingsSpamShieldIndicator['session'],
			Div::getFormStartFromSession($mail->getForm()->getUid(), $this->settings)
		);
		$this->uniqueCheck($mail, $settingsSpamShieldIndicator['unique']);
		$this->blacklistStringCheck($mail, $settingsSpamShieldIndicator['blacklistString']);
		$this->blacklistIpCheck($settingsSpamShieldIndicator['blacklistIp'], GeneralUtility::getIndpEnv('REMOTE_ADDR'));
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
	protected function linkCheck(Mail $mail, $indication = 1.0, $limit = 2) {
		if (!$indication) {
			return;
		}

		$linkAmount = 0;
		foreach ($mail->getAnswers() as $answer) {
			if (is_array($answer->getValue())) {
				continue;
			}
			preg_match_all('@http://|https://|ftp://@', $answer->getValue(), $result);
			if (isset($result[0])) {
				$linkAmount += count($result[0]);
			}
		}

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
	protected function nameCheck(Mail $mail, $indication = 1.0) {
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

		if (!empty($firstname) && !empty($lastname) && $firstname === $lastname) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
			return;
		}
	}

	/**
	 * Session Check: Checks if session was started correct on form delivery
	 *
	 * @param float $indication Indication if check fails
	 * @param int $timeFromSession
	 * @return void
	 */
	protected function sessionCheck($indication = 1.0, $timeFromSession) {
		if (!$indication || $this->referrer === 'optinConfirm') {
			return;
		}

		if (empty($timeFromSession)) {
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
	protected function uniqueCheck(Mail $mail, $indication = 1.0) {
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
	protected function blacklistStringCheck(Mail $mail, $indication = 1.0) {
		if (!$indication) {
			return;
		}
		$blacklist = GeneralUtility::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistStringValues'], TRUE);

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
	 * @param string $userIpAddress Visitors IP address
	 * @return void
	 */
	protected function blacklistIpCheck($indication = 1.0, $userIpAddress) {
		if (!$indication) {
			return;
		}
		$blacklist = GeneralUtility::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistIpValues'], TRUE);

		if (in_array($userIpAddress, $blacklist)) {
			$this->increaseSpamIndicator($indication);
			$this->addMessage(__FUNCTION__ . ' failed');
			return;
		}
	}

	/**
	 * calculate spam factor for this mail
	 * 		spam formula with asymptote 1 (100%)
	 *
	 * @return void
	 */
	protected function calculateMailSpamFactor() {
		$calculatedMailSpamFactor = 0;
		if ($this->getSpamIndicator() > 0) {
			$calculatedMailSpamFactor = -1 / $this->getSpamIndicator() + 1;
		}
		$this->setCalculatedMailSpamFactor($calculatedMailSpamFactor);
	}

	/**
	 * Send spam notification mail to admin
	 *
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @return void
	 */
	protected function sendSpamNotificationMail($mail) {
		if (!GeneralUtility::validEmail($this->settings['spamshield.']['email'])) {
			return;
		}
		$variables = array(
			'mail' => $mail,
			'pid' => $this->typoScriptFrontendController->id,
			'calculatedMailSpamFactor' => $this->getCalculatedMailSpamFactor(TRUE),
			'messages' => $this->getMessages(),
			'ipAddress' => (!Configuration::isDisableIpLogActive() ? GeneralUtility::getIndpEnv('REMOTE_ADDR') : '')
		);
		Div::sendPlainMail(
			$this->settings['spamshield.']['email'],
			'powermail@' . GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'),
			$this->settings['spamshield.']['emailSubject'],
			$this->createSpamNotificationMailBody($this->settings['spamshield.']['emailTemplate'], $variables)
		);
	}

	/**
	 * Create bodytext for spamnotification mail
	 *
	 * @param string $path relative path to mail
	 * @param array $multipleAssign
	 * @return string
	 */
	protected function createSpamNotificationMailBody($path, $multipleAssign = array()) {
		$rootPath = GeneralUtility::getFileAbsFileName('EXT:powermail/Resources/Private/');
		/** @var StandaloneView $standAloneView */
		$standAloneView = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		$standAloneView->getRequest()->setControllerExtensionName('Powermail');
		$standAloneView->getRequest()->setPluginName('Pi1');
		$standAloneView->setFormat('html');
		$standAloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($path));
		$standAloneView->setLayoutRootPaths(array($rootPath . 'Layouts'));
		$standAloneView->setPartialRootPaths(array($rootPath . 'Partials'));
		$standAloneView->assignMultiple($multipleAssign);
		return $standAloneView->render();
	}

	/**
	 * Format for Spamfactor (0.23 => 23%)
	 *
	 * @param float $factor
	 * @return string
	 */
	protected function formatSpamFactor($factor) {
		return number_format(($factor * 100), 0) . '%';
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
	 * @return float
	 */
	public function getSpamFactorLimit() {
		return $this->spamFactorLimit;
	}

	/**
	 * @param float $spamFactorLimit
	 * @return void
	 */
	public function setSpamFactorLimit($spamFactorLimit) {
		$this->spamFactorLimit = $spamFactorLimit;
	}

	/**
	 * @param bool $readableOutput
	 * @return float
	 */
	public function getCalculatedMailSpamFactor($readableOutput = FALSE) {
		$calculatedMailSpamFactor = $this->calculatedMailSpamFactor;
		if ($readableOutput) {
			$calculatedMailSpamFactor = $this->formatSpamFactor($calculatedMailSpamFactor);
		}
		return $calculatedMailSpamFactor;
	}

	/**
	 * Return if spam tolerance limit is reached
	 *
	 * @return bool
	 */
	public function isSpamToleranceLimitReached() {
		return $this->getCalculatedMailSpamFactor() >= $this->getSpamFactorLimit();
	}

	/**
	 * @param float $calculatedMailSpamFactor
	 * @return void
	 */
	public function setCalculatedMailSpamFactor($calculatedMailSpamFactor) {
		$this->calculatedMailSpamFactor = $calculatedMailSpamFactor;
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

	/**
	 * Save Spam Factor in session for db storage
	 *
	 * @return void
	 */
	protected function saveSpamFactorInSession() {
		$this->typoScriptFrontendController->fe_user->setKey(
			'ses',
			'powermail_spamfactor',
			$this->getCalculatedMailSpamFactor(TRUE)
		);
		$this->typoScriptFrontendController->storeSessionData();
	}

	/**
	 * Save spam properties in development log
	 *
	 * @return void
	 */
	protected function saveSpamPropertiesInDevelopmentLog() {
		if (empty($this->settings['debug.']['spamshield'])) {
			return;
		}
		GeneralUtility::devLog(
			'Spamshield (Spamfactor ' . $this->getCalculatedMailSpamFactor(TRUE) . ')',
			'powermail',
			0,
			$this->getMessages()
		);
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->piVars = GeneralUtility::_GP('tx_powermail_pi1');
		$this->referrer = $this->piVars['__referrer']['@action'];
		$this->typoScriptFrontendController = $GLOBALS['TSFE'];
		$this->configurationArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
		$this->setSpamFactorLimit($this->settings['spamshield.']['factor'] / 100);
	}
}