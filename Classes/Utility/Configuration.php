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
 * Configuration Utility class
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class Configuration {

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
}