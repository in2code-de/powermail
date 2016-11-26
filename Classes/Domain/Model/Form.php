<?php
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * Class Form
 * @package In2code\Powermail\Domain\Model
 */
class Form extends AbstractEntity
{

    const TABLE_NAME = 'tx_powermail_domain_model_form';

    /**
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $css = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Page>
     */
    protected $pages;

    /**
     * Container for pages with title as key
     *
     * @var array
     */
    protected $pagesByTitle = [];

    /**
     * Container for pages with uid as key
     *
     * @var array
     */
    protected $pagesByUid = [];

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the css
     *
     * @return string $css
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Sets the css
     *
     * @param string $css
     * @return void
     */
    public function setCss($css)
    {
        $this->css = $css;
    }

    /**
     * Returns the pages
     *
     * @return ObjectStorage
     */
    public function getPages()
    {
        // if elementbrowser instead of IRRE (sorting workarround)
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
            $formSorting = GeneralUtility::trimExplode(',', $formRepository->getPagesValue($this->uid), true);
            $formSorting = array_flip($formSorting);
            $pageArray = [];
            foreach ($this->pages as $page) {
                $pageArray[$formSorting[$page->getUid()]] = $page;
            }
            ksort($pageArray);
            return $pageArray;
        }

        return $this->pages;
    }

    /**
     * Sets the pages
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     * @return void
     */
    public function setPages(ObjectStorage $pages)
    {
        $this->pages = $pages;
    }

    /**
     * Check if this form has an upload field
     *
     * @return bool
     */
    public function hasUploadField()
    {
        foreach ($this->getPages() as $page) {
            /** @var Field $field */
            foreach ($page->getFields() as $field) {
                if ($field->dataTypeFromFieldType($field->getType()) === 3) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return pages as an array with title as key.
     *
     *      Example to get a page object by title use:
     *          PHP: $form->getPagesByTitle()['page1'];
     *          FLUID: {form.pagesByTitle.page1}
     *
     * @return array
     */
    public function getPagesByTitle()
    {
        if (empty($this->pagesByTitle)) {
            $pagesArray = $this->getPages()->toArray();
            $this->pagesByTitle = array_combine(array_map(function (Page $page) {
                return StringUtility::cleanString($page->getTitle());
            }, $pagesArray), $pagesArray);
        }
        return $this->pagesByTitle;
    }

    /**
     * Return pages as an array with uid as key.
     *
     *      Example to get a page object by uid use:
     *          PHP: $form->getPagesByUid()[123];
     *          FLUID: {form.pagesByUid.123}
     *
     * @return array
     */
    public function getPagesByUid()
    {
        if (empty($this->pagesByUid)) {
            $pagesArray = $this->getPages()->toArray();
            $this->pagesByUid = array_combine(array_map(function (Page $page) {
                return $page->getUid();
            }, $pagesArray), $pagesArray);
        }
        return $this->pagesByUid;
    }
}
