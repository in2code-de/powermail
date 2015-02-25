<?php
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Stefan Neufeind <info (at) speedpartner.de, SpeedPartner GmbH
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
 * Extends the regular standalone template view with
 * 		the possibilities to use multiple paths (like TemplateView is able to use).
 */
class StandaloneViewMultiplePaths extends StandaloneView {

	/**
	 * Path(s) to the template root.
	 * 		If NULL, then $this->templateRootPathPattern will be used.
	 *
	 * @var array
	 */
	protected $templateRootPaths = NULL;

	/**
	 * Path(s) to the partial root.
	 * 		If NULL, then $this->partialRootPathPattern will be used.
	 *
	 * @var array
	 */
	protected $partialRootPaths = NULL;

	/**
	 * Path(s) to the layout root.
	 * 		If NULL, then $this->layoutRootPathPattern will be used.
	 *
	 * @var array
	 */
	protected $layoutRootPaths = NULL;

	/**
	 * Sets the absolute path to the folder that contains Fluid layout files
	 *
	 * @param string $layoutRootPath Fluid layout root path
	 * @return void
	 * @deprecated Provided by the StandaloneView-API, but deprecated
	 * @api
	 */
	public function setLayoutRootPath($layoutRootPath) {
		GeneralUtility::logDeprecatedFunction();
		$this->setLayoutRootPaths(array($layoutRootPath));
	}

	/**
	 * Sets the absolute paths to the folders that contains Fluid layout files
	 *
	 * @param array $layoutRootPaths Fluid layout root path(s)
	 * @return void
	 * @api
	 */
	public function setLayoutRootPaths($layoutRootPaths) {
		$this->layoutRootPaths = $layoutRootPaths;
	}

	/**
	 * Returns the absolute path to the folder that contains Fluid layout files
	 *
	 * @return string Fluid layout root path
	 * @deprecated Provided by the StandaloneView-API, but deprecated
	 * @api
	 */
	public function getLayoutRootPath() {
		GeneralUtility::logDeprecatedFunction();
		$layoutRootPaths = $this->getLayoutRootPaths();
		return $layoutRootPaths[0];
	}

	/**
	 * Returns the absolute paths to the folders that contains Fluid layout files
	 *
	 * @return array Fluid layout root path(s)
	 * @throws \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException
	 * @api
	 */
	public function getLayoutRootPaths() {
		if ($this->layoutRootPaths === NULL && $this->templatePathAndFilename === NULL) {
			throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
				'No layout root paths have been specified. Use setLayoutRootPaths().',
				1410653161
			);
		}
		if ($this->layoutRootPaths === NULL) {
			$this->layoutRootPaths = array(dirname($this->templatePathAndFilename) . '/Layouts');
		}
		return $this->layoutRootPaths;
	}

	/**
	 * Sets the absolute path to the folder that contains Fluid partial files.
	 *
	 * @param string $partialRootPath Fluid partial root path
	 * @return void
	 * @deprecated Provided by the StandaloneView-API, but deprecated
	 * @api
	 */
	public function setPartialRootPath($partialRootPath) {
		GeneralUtility::logDeprecatedFunction();
		$this->setPartialRootPaths(array($partialRootPath));
	}

	/**
	 * Sets the absolute paths to the folders that contains Fluid partial files.
	 *
	 * @param array $partialRootPaths Fluid partial root paths
	 * @return void
	 * @api
	 */
	public function setPartialRootPaths($partialRootPaths) {
		$this->partialRootPaths = $partialRootPaths;
	}

	/**
	 * Returns the absolute path to the folder that contains Fluid partial files
	 *
	 * @return string Fluid partial root path
	 * @deprecated Provided by the StandaloneView-API, but deprecated
	 * @api
	 */
	public function getPartialRootPath() {
		GeneralUtility::logDeprecatedFunction();
		$partialRootPaths = $this->getPartialRootPaths();
		return $partialRootPaths[0];
	}

	/**
	 * Returns the absolute paths to the folders that contains Fluid partial files
	 *
	 * @throws \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException
	 * @return array Fluid partial root paths
	 * @api
	 */
	public function getPartialRootPaths() {
		if ($this->partialRootPaths === NULL && $this->templatePathAndFilename === NULL) {
			throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
				'No partial root paths have been specified. Use setPartialRootPath().',
				1410653162
			);
		}
		if ($this->partialRootPaths === NULL) {
			$this->partialRootPaths = array(dirname($this->templatePathAndFilename) . '/Partials');
		}
		return $this->partialRootPaths;
	}

	/**
	 * Resolve the path and file name of the layout file, based on
	 * $this->getLayoutRootPaths() and request format
	 *
	 * In case a layout has already been set with setLayoutPathAndFilename(),
	 * this method returns that path, otherwise a path and filename will be
	 * resolved using the layoutPathAndFilenamePattern.
	 *
	 * @param string $layoutName Name of the layout to use. If none use "Default
	 * @return string Path and filename of layout files
	 * @throws \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException
	 */
	protected function getLayoutPathAndFilename($layoutName = 'Default') {
		$layoutRootPaths = $this->getLayoutRootPaths();
		foreach ($layoutRootPaths as $pathIndex => $layoutRootPath) {
			if (!is_dir($layoutRootPath)) {
				unset($layoutRootPaths[$pathIndex]);
			}
		}
		if (empty($layoutRootPaths)) {
			throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
				'Layout root paths do not exist.',
				1410653917
			);
		}
		$possibleLayoutPaths = array();
		foreach ($layoutRootPaths as $layoutRootPath) {
			$possibleLayoutPaths[] = GeneralUtility::fixWindowsFilePath($layoutRootPath . '/' . $layoutName . '.' . $this->getRequest()->getFormat());
			$possibleLayoutPaths[] = GeneralUtility::fixWindowsFilePath($layoutRootPath . '/' . $layoutName);
		}
		foreach ($possibleLayoutPaths as $layoutPathAndFilename) {
			if (is_file($layoutPathAndFilename)) {
				return $layoutPathAndFilename;
			}
		}
		throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
			'Could not load layout file. Tried following paths: "' . implode('", "', $possibleLayoutPaths) . '".',
			1288092555
		);
	}


	/**
	 * Resolve the partial path and filename
	 * 		based on $this->getPartialRootPaths() and request format
	 *
	 * @param string $partialName The name of the partial
	 * @return string the full path which should be used. The path definitely exists.
	 * @throws \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException
	 */
	protected function getPartialPathAndFilename($partialName) {
		$partialRootPaths = $this->getPartialRootPaths();
		foreach ($partialRootPaths as $pathIndex => $partialRootPath) {
			if (!is_dir($partialRootPath)) {
				unset($partialRootPaths[$pathIndex]);
			}
		}
		if (empty($partialRootPaths)) {
			throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
				'Partial root paths do not exist.',
				1410653918
			);
		}
		$possiblePartialPaths = array();
		foreach ($partialRootPaths as $partialRootPath) {
			$possiblePartialPaths[] = GeneralUtility::fixWindowsFilePath($partialRootPath . '/' . $partialName . '.' . $this->getRequest()->getFormat());
			$possiblePartialPaths[] = GeneralUtility::fixWindowsFilePath($partialRootPath . '/' . $partialName);
		}
		foreach ($possiblePartialPaths as $partialPathAndFilename) {
			if (is_file($partialPathAndFilename)) {
				return $partialPathAndFilename;
			}
		}
		throw new \TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException(
			'Could not load partial file. Tried following paths: "' . implode('", "', $possiblePartialPaths) . '".',
			1288092556
		);
	}
}