<?php
namespace In2code\Powermail\Utility;

use In2code\Powermail\Domain\Model\Mail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 in2code.de
 *  Alex Kellner <alexander.kellner@in2code.de>
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
 * Class TemplateUtility
 *
 * @package In2code\Powermail\Utility
 */
class TemplateUtility {

	/**
	 * Get absolute path for templates with fallback
	 * 		In case of multiple paths this will just return the first one.
	 * 		See getTemplateFolders() for an array of paths.
	 *
	 * @param string $part "template", "partial", "layout"
	 * @return string
	 * @see getTemplateFolders()
	 */
	public static function getTemplateFolder($part = 'template') {
		$matches = self::getTemplateFolders($part);
		return !empty($matches) ? $matches[0] : '';
	}

	/**
	 * Get absolute paths for templates with fallback
	 * 		Returns paths from *RootPaths and *RootPath and "hardcoded"
	 * 		paths pointing to the EXT:powermail-resources.
	 *
	 * @param string $part "template", "partial", "layout"
	 * @param boolean $returnAllPaths Default: FALSE, If FALSE only paths
	 * 		for the first configuration (Paths, Path, hardcoded)
	 * 		will be returned. If TRUE all (possible) paths will be returned.
	 * @return array
	 */
	public static function getTemplateFolders($part = 'template', $returnAllPaths = FALSE) {
		$templatePaths = array();

		/** @var ConfigurationManager $configurationManager */
		$configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
			->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
		);
		if (!empty($extbaseFrameworkConfiguration['view'][$part . 'RootPaths'])) {
			$templatePaths = $extbaseFrameworkConfiguration['view'][$part . 'RootPaths'];
			$templatePaths = array_values($templatePaths);
		}
		if ($returnAllPaths || empty($templatePaths)) {
			$path = $extbaseFrameworkConfiguration['view'][$part . 'RootPath'];
			if (!empty($path)) {
				$templatePaths[] = $path;
			}
		}
		if ($returnAllPaths || empty($templatePaths)) {
			$templatePaths[] = 'EXT:powermail/Resources/Private/' . ucfirst($part) . 's/';
		}
		$templatePaths = array_unique($templatePaths);
		$absoluteTemplatePaths = array();
		foreach ($templatePaths as $templatePath) {
			$absoluteTemplatePaths[] = GeneralUtility::getFileAbsFileName($templatePath);
		}
		return $absoluteTemplatePaths;
	}

	/**
	 * Return path and filename for a file or path.
	 * 		Only the first existing file/path will be returned.
	 * 		respect *RootPaths and *RootPath
	 *
	 * @param string $relativePathAndFilename e.g. Email/Name.html
	 * @param string $part "template", "partial", "layout"
	 * @return string Filename/path
	 */
	public static function getTemplatePath($relativePathAndFilename, $part = 'template') {
		$matches = self::getTemplatePaths($relativePathAndFilename, $part);
		return !empty($matches) ? $matches[0] : '';
	}

	/**
	 * Return path and filename for one or many files/paths.
	 * 		Only existing files/paths will be returned.
	 * 		respect *RootPaths and *RootPath
	 *
	 * @param string $relativePathAndFilename Path/filename (Email/Name.html) or path
	 * @param string $part "template", "partial", "layout"
	 * @return array All existing matches found
	 */
	public static function getTemplatePaths($relativePathAndFilename, $part = 'template') {
		$absolutePathAndFilenameMatches = array();
		$absolutePaths = self::getTemplateFolders($part, TRUE);
		foreach ($absolutePaths as $absolutePath) {
			if (file_exists($absolutePath . $relativePathAndFilename)) {
				$absolutePathAndFilenameMatches[] = $absolutePath . $relativePathAndFilename;
			}
		}
		return $absolutePathAndFilenameMatches;
	}

	/**
	 * Get standaloneview with default properties
	 *
	 * @param string $controllerExtensionName
	 * @param string $pluginName
	 * @param string $format
	 * @return StandaloneView
	 */
	public static function getDefaultStandAloneView($controllerExtensionName = 'Powermail', $pluginName = 'Pi1', $format = 'html') {
		/** @var StandaloneView $standaloneView */
		$standaloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
			->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standaloneView->getRequest()->setControllerExtensionName($controllerExtensionName);
		$standaloneView->getRequest()->setPluginName($pluginName);
		$standaloneView->setFormat($format);
		return $standaloneView;
	}

	/**
	 * This functions renders the powermail_all Template (e.g. useage in Mails)
	 *
	 * @param Mail $mail
	 * @param string $section Choose a section (web or mail)
	 * @param array $settings TypoScript Settings
	 * @param string $type "createAction", "confirmationAction", "sender", "receiver"
	 * @return string content parsed from powermailAll HTML Template
	 */
	public static function powermailAll(Mail $mail, $section = 'web', $settings = array(), $type = NULL) {
		$templatePathAndFilename = self::getTemplatePath('Form/PowermailAll.html');
		/** @var StandaloneView $standaloneView */
		$standaloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
			->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standaloneView->setTemplatePathAndFilename($templatePathAndFilename);
		$standaloneView->setLayoutRootPaths(self::getTemplateFolders('layout'));
		$standaloneView->setPartialRootPaths(self::getTemplateFolders('partial'));
		$standaloneView->assignMultiple(
			array(
				'mail' => $mail,
				'section' => $section,
				'settings' => $settings,
				'type' => $type
			)
		);
		return $standaloneView->render();
	}

	/**
	 * Parse String with Fluid View
	 *
	 * @param string $string Any string
	 * @param array $variables Variables
	 * @return string Parsed string
	 */
	public static function fluidParseString($string, $variables = array()) {
		if (empty($string) || empty($GLOBALS['TYPO3_DB'])) {
			return $string;
		}
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $standaloneView */
		$standaloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
			->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standaloneView->setTemplateSource($string);
		$standaloneView->assignMultiple($variables);
		return $standaloneView->render();
	}
}
