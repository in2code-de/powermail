<?php
namespace In2code\Powermail\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * ConfigurationUtility class
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class ConfigurationUtility {

	/**
	 * Check if disableIpLog is active
	 *
	 * @return bool
	 */
	public static function isDisableIpLogActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['disableIpLog'] === '1';
	}

	/**
	 * Check if disableMarketingInformation is active
	 *
	 * @return bool
	 */
	public static function isDisableMarketingInformationActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['disableMarketingInformation'] === '1';
	}

	/**
	 * Check if disableBackendModule is active
	 *
	 * @return bool
	 */
	public static function isDisableBackendModuleActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['disableBackendModule'] === '1';
	}

	/**
	 * Check if disablePluginInformation is active
	 *
	 * @return bool
	 */
	public static function isDisablePluginInformationActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['disablePluginInformation'] === '1';
	}

	/**
	 * Check if enableCaching is active
	 *
	 * @return bool
	 */
	public static function isEnableCachingActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['enableCaching'] === '1';
	}

	/**
	 * Check if enableTableGarbageCollection is active
	 *
	 * @return bool
	 */
	public static function isEnableTableGarbageCollectionActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['enableTableGarbageCollection'] === '1';
	}

	/**
	 * Check if replaceIrreWithElementBrowser is active
	 *
	 * @return bool
	 */
	public static function isReplaceIrreWithElementBrowserActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['replaceIrreWithElementBrowser'] === '1';
	}

	/**
	 * Check if l10n_mode_merge is active
	 *
	 * @return bool
	 */
	public static function isL10nModeMergeActive() {
		$extensionConfiguration = self::getExtensionConfiguration();
		return $extensionConfiguration['l10n_mode_merge'] === '1';
	}

	/**
	 * Get extension configuration from LocalConfiguration.php
	 *
	 * @return array
	 */
	public static function getExtensionConfiguration() {
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);
	}

	/**
	 * Merges Flexform, TypoScript and Extension Manager Settings (up to 2 levels)
	 *        Note: It's not possible to have the same field in TS and Flexform
	 *        and if FF value is empty, we want the TypoScript value instead
	 *
	 * @param array $settings All settings
	 * @param string $typoScriptLevel Startpoint
	 * @return void
	 */
	public static function mergeTypoScript2FlexForm(&$settings, $typoScriptLevel = 'setup') {
		// config
		$temporarySettings = array();
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['powermail']);

		if (isset($settings[$typoScriptLevel]) && is_array($settings[$typoScriptLevel])) {
			// copy typoscript part to conf part
			$temporarySettings = $settings[$typoScriptLevel];
		}

		if (isset($settings['flexform']) && is_array($settings['flexform'])) {
			// copy flexform part to conf part
			$temporarySettings = array_merge((array)$temporarySettings, (array)$settings['flexform']);
		}

		// merge ts and ff (loop every flexform)
		foreach ($temporarySettings as $key1 => $value1) {
			// 1. level
			if (!is_array($value1)) {
				// only if this key exists in ff and ts
				if (isset($settings[$typoScriptLevel][$key1]) && isset($settings['flexform'][$key1])) {
					// only if ff is empty and ts not
					if ($settings[$typoScriptLevel][$key1] && !$settings['flexform'][$key1]) {
						// overwrite with typoscript settings
						$temporarySettings[$key1] = $settings[$typoScriptLevel][$key1];
					}
				}
			} else {
				// 2. level
				foreach ($value1 as $key2 => $value2) {
					$value2 = NULL;

					// only if this key exists in ff and ts
					if (isset($settings[$typoScriptLevel][$key1][$key2]) && isset($settings['flexform'][$key1][$key2])) {
						// only if ff is empty and ts not
						if ($settings[$typoScriptLevel][$key1][$key2] && !$settings['flexform'][$key1][$key2]) {
							// overwrite with typoscript settings
							$temporarySettings[$key1][$key2] = $settings[$typoScriptLevel][$key1][$key2];
						}
					}
				}
			}
		}

		// merge ts and ff (loop every typoscript)
		foreach ((array)$settings[$typoScriptLevel] as $key1 => $value1) {
			// 1. level
			if (!is_array($value1)) {
				// only if this key exists in ts and not in ff
				if (isset($settings[$typoScriptLevel][$key1]) && !isset($settings['flexform'][$key1])) {
					// set value from ts
					$temporarySettings[$key1] = $value1;
				}
			} else {
				// 2. level
				foreach ($value1 as $key2 => $value2) {
					// only if this key exists in ts and not in ff
					if (isset($settings[$typoScriptLevel][$key1][$key2]) && !isset($settings['flexform'][$key1][$key2])) {
						// set value from ts
						$temporarySettings[$key1][$key2] = $value2;
					}
				}
			}
		}

		// add global config
		$temporarySettings['global'] = $confArr;

		$settings = $temporarySettings;
		unset($temporarySettings);
	}
}