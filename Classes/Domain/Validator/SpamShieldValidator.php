<?php
class Tx_Powermail_Domain_Validator_SpamShieldValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * Spam indication start value
	 *
	 * @var integer
	 */
	private $spamIndicator = 0.5;

	/**
	 * TypoScript Settings
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Instance for Misc Functions
	 *
	 * @var object
	 */
	protected $div;

	/**
	 * Spam-Validation of given Params
	 * 		see powermail/doc/SpamDetection for formula
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
		t3lib_utility_Debug::debug($params, __FILE__ . " " . __LINE__);
		$this->div = t3lib_div::makeInstance('Tx_Powermail_Utility_Div');
		$spamFactor = $this->settings['spamshield.']['factor'] / 100;

		// Different checks to increase spam indicator
		$this->linkCheck($params, $this->settings['spamshield.']['indicator.']['name'], $this->settings['spamshield.']['indicator.']['linkLimit']);
		$this->nameCheck($params, $this->settings['spamshield.']['indicator.']['name']);
		$this->sessionCheck($this->settings['spamshield.']['indicator.']['session']);
		$this->uniqueCheck($params, $this->settings['spamshield.']['indicator.']['unique']);
		$this->blacklistStringCheck($params, $this->settings['spamshield.']['indicator.']['blacklistString']);
		$this->blacklistIpCheck($this->settings['spamshield.']['indicator.']['blacklistIp']);

		// spam formula with asymptote 1 (100%)
		$thisSpamFactor = -1 / $this->spamIndicator + 1;

		// if spam
		if ($thisSpamFactor >= $spamFactor) {
			$this->addError('spam_details', number_format(($thisSpamFactor * 100), 0) . '%');

			// TODO send mail to admin
			return false;
		}

		return true;
	}

	/**
	 * Link Check: Counts numbers of links in message
	 *
	 * @param $params array Given params
	 * @param $indication float Indication if check fails
	 * @param $limit integer Limit of allowed links in mail
	 * @return void
	 */
	private function linkCheck($params, $indication = 1, $limit = 2) {
		if (!$indication) {
			return;
		}

		// check numbers of links
		$linkAmount = 0;
		foreach ((array) $params as $value) {
			if (is_array($value)) {
				continue;
			}
			preg_match_all('@http://|https://|ftp.@', $value, $result);
			if (isset($result[0])) {
				$linkAmount += count($result[0]); // add numbers of http://
			}
		}

		// if check failes
		if ($linkAmount > $limit) {
			$this->spamIndicator += $indication;
		}
		return;
	}

	/**
	 * Name Check: Compares first- and lastname (shouldn't be the same)
	 *
	 * @param $params array Given params
	 * @param $indication float Indication if check fails
	 * @return void
	 */
	private function nameCheck($params, $indication = 1) {
		if (!$indication) {
			return;
		}
		$keys_firstname = array(
			'first_name',
			'firstname',
			'vorname'
		);
		$keys_lastname = array(
			'last_name',
			'lastname',
			'sur_name',
			'surname',
			'nachname',
			'name'
		);

		// find out first- and lastname (marker should be {firstname} and {lastname}
		foreach ((array) $params as $field => $value) {
			if (is_array($value)) {
				continue;
			}
			$marker = $this->div->getMarkerFromField($field);
			if (in_array($marker, $keys_firstname)) {
				$firstname = $value;
			}
			if (in_array($marker, $keys_lastname)) {
				$lastname = $value;
			}
		}

		// if check failes
		if (isset($firstname) && isset($lastname)) {
			if ($firstname && $firstname == $lastname) {
				$this->spamIndicator += $indication;
				return;
			}
		}
	}

	/**
	 * Session Check: Checks if session was started correct on form delivery
	 *
	 * @param $indication float Indication if check fails
	 * @return void
	 */
	private function sessionCheck($indication = 1) {
		if (!$indication) {
			return;
		}
		$gp = t3lib_div::_GP('tx_powermail_pi1');
		$formUid = $gp['form'];
		$time = $this->div->getFormStartFromSession($formUid);

		// if check failes
		if (!isset($time) || !$time) {
			$this->spamIndicator += $indication;
		}
		return;
	}

	/**
	 * Unique Check: Checks if values in given params are different
	 *
	 * @param $params array Given params
	 * @param $indication float Indication if check fails
	 * @return void
	 */
	private function uniqueCheck($params, $indication = 1) {
		if (!$indication) {
			return;
		}

		// don't want values in second level (from checkboxes e.g.)
		$arr = array();
		foreach ((array) $params as $value) {
			if (is_array($value)) {
				continue;
			}
			$arr[] = $value;
		}

		// if check failes
		if (count($arr) != count(array_unique($arr))) {
			$this->spamIndicator += $indication;
			return;
		}
	}

	/**
	 * Blacklist String Check: Check if a blacklisted word is in given values
	 *
	 * @param $params array Given params
	 * @param $indication float Indication if check fails
	 * @return void
	 */
	private function blacklistStringCheck($params, $indication = 1) {
		if (!$indication) {
			return;
		}
		$blacklist = t3lib_div::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistStringValues'], 1);

		// if check failes
		foreach ((array) $params as $value) {
			foreach ((array) $blacklist as $blackword) {
				if (is_array($value)) {
					continue;
				}
				if (stristr($value, $blackword)) {
					$this->spamIndicator += $indication;
					return;
				}
			}
		}
		return;
	}

	/**
	 * Blacklist IP-Address Check: Check if Senders IP is blacklisted
	 *
	 * @param $indication float Indication if check fails
	 * @return void
	 */
	private function blacklistIpCheck($indication = 1) {
		if (!$indication) {
			return;
		}
		$blacklist = t3lib_div::trimExplode(',', $this->settings['spamshield.']['indicator.']['blacklistIpValues'], 1);

		// if check failes
		if (in_array(t3lib_div::getIndpEnv('REMOTE_ADDR'), $blacklist)) {
			$this->spamIndicator += $indication;
			return;
		}
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$typoScriptSetup = $configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}
}
?>