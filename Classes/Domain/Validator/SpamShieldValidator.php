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
	 * Spam-Validation of given Params
	 * 		see powermail/doc/SpamDetection for formula
	 *
	 * @param $params
	 * @return bool
	 */
	public function isValid($params) {
//		t3lib_utility_Debug::debug($params, __FILE__ . " " . __LINE__);
		$spamFactor = $this->settings['spamshield.']['factor'] / 100;

		// Different checks to increase spam indicator
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
			if (!is_array($value)) {
				$arr[] = $value;
			}
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
				if (!is_array($value)) {
					if (stristr($value, $blackword)) {
						$this->spamIndicator += $indication;
						return;
					}
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