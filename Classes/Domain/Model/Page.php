<?php
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
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
 * PageModel
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Page extends AbstractEntity
{

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * css
     *
     * @var string
     */
    protected $css = '';

    /**
     * Powermail Fields
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Field>
     */
    protected $fields = null;

    /**
     * Powermail Forms
     *
     * @var \In2code\Powermail\Domain\Model\Form
     * @lazy
     */
    protected $forms = null;

    /**
     * sorting
     *
     * @var integer
     */
    protected $sorting = 0;

    /**
     * formRepository
     *
     * @var \In2code\Powermail\Domain\Repository\FormRepository
     * @inject
     */
    protected $formRepository;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->fields = new ObjectStorage();
    }

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
     * Adds a Fields
     *
     * @param Field $field
     * @return void
     */
    public function addField(Field $field)
    {
        $this->fields->attach($field);
    }

    /**
     * Removes a Fields
     *
     * @param Field $fieldToRemove
     * @return void
     */
    public function removeField(Field $fieldToRemove)
    {
        $this->fields->detach($fieldToRemove);
    }

    /**
     * Returns the fields
     *
     * @return ObjectStorage
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sets the fields
     *
     * @var ObjectStorage
     * @return void
     */
    public function setFields(ObjectStorage $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Returns the sorting
     *
     * @return integer $sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Sets the sorting
     *
     * @param integer $sorting
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @param Form $forms
     * @return void
     */
    public function setForms($forms)
    {
        $this->forms = $forms;
    }

    /**
     * @return Form
     */
    public function getForms()
    {
        // if elementbrowser instead of IRRE (get related form)
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            return $this->formRepository->findByPages($this->uid);
        }
        return $this->forms;
    }
}
