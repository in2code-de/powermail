<?php
namespace In2code\Powermail\Hook;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class to autofill field marker with value from title e.g. {firstname}
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CreateMarker
{

    /**
     * @var null|ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var int|string
     */
    protected $uid = '';

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * React if one of this tables is in game
     *
     * @var array
     */
    protected $allowedTableNames = [
        Form::TABLE_NAME,
        Page::TABLE_NAME,
        Field::TABLE_NAME
    ];

    /**
     * Contains all (new and existing fields) with name and markers
     *
     * [
     *      12 => Field,
     *      13 => Field
     * ]
     *
     * @var array
     */
    protected $fieldArray = [];

    /**
     * @param string $status mode of change
     * @param string $table the table that gets changed
     * @param string $uid identifier of the record
     * @param array $properties the properties that can be manipulated before storing
     * @return void
     */
    public function initialize($status, $table, $uid, &$properties)
    {
        $this->status = $status;
        $this->table = $table;
        $this->uid = $uid;
        $this->properties = &$properties;
        $this->objectManager = ObjectUtility::getObjectManager();
        $this->data = (array)GeneralUtility::_GP('data');
        $this->addExistingFields();
        $this->addNewFields();
    }

    /**
     * Fill the marker field initially from title and check if they are unique in a form
     *
     * @param string $status mode of change
     * @param string $table the table that gets changed
     * @param string $uid identifier of the record
     * @param array $properties the properties that can be manipulated before storing
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $uid, &$properties)
    {
        if ($this->shouldProcess($table)) {
            $this->initialize($status, $table, $uid, $properties);
            /** @var GetNewMarkerNamesForFormService $markerService */
            $markerService = $this->objectManager->get(GetNewMarkerNamesForFormService::class);
            $markers = $markerService->makeUniqueValueInArray($this->fieldArray);

            if ($this->table === Field::TABLE_NAME) {
                $this->setMarkerForFields($markers);
                $this->renameMarker($markers);
                $this->cleanMarkersInLocalizedFields();
            }
            $this->checkAndRenameMarkers($markers);
        }
    }

    /**
     * Initially set marker names in fields
     *
     * @param array $markers
     * @return void
     */
    protected function setMarkerForFields(array $markers)
    {
        if ($this->shouldProcessField()) {
            if (isset($this->properties['marker']) && empty($this->properties['marker'])) {
                $this->setMarkerProperty(GetNewMarkerNamesForFormService::getRandomMarkerName());
            }
            if (!empty($markers[$this->uid])) {
                $this->setMarkerProperty($markers[$this->uid]);
            }
        }
    }

    /**
     * Rename marker name if it's empty or duplicated
     *
     * @param array $markers
     * @return void
     */
    protected function renameMarker(array $markers)
    {
        if ($this->shouldRenameMarker($markers)) {
            $this->setMarkerProperty($markers[$this->uid]);
        }
    }

    /**
     * Marker should be empty on localized fields
     *
     * @return void
     */
    protected function cleanMarkersInLocalizedFields()
    {
        if (!empty($this->properties['sys_language_uid']) && $this->properties['sys_language_uid'] > 0) {
            $this->properties['marker'] = '';
        }
    }

    /**
     * Check if persisted fields should have a different marker name and rename it if it's necessary
     *
     * @param array $markers
     * @return void
     */
    protected function checkAndRenameMarkers(array $markers)
    {
        foreach ($markers as $uid => $marker) {
            $row = ObjectUtility::getDatabaseConnection()->exec_SELECTgetSingleRow(
                'marker',
                Field::TABLE_NAME,
                'uid=' . (int)$uid
            );
            if ($row['marker'] !== $marker) {
                ObjectUtility::getDatabaseConnection()->exec_UPDATEquery(
                    Field::TABLE_NAME,
                    'uid=' . (int)$uid,
                    ['marker' => $marker]
                );
            }
        }
    }

    /**
     * Set new property
     *
     * @param $marker
     * @return void
     */
    protected function setMarkerProperty($marker)
    {
        $this->properties['marker'] = $marker;
    }

    /**
     * Add existing fields from database to field array
     *
     * @return void
     */
    protected function addExistingFields()
    {
        $fieldProperties = $this->getFieldProperties();
        foreach ($fieldProperties as $properties) {
            $this->addField($this->makeFieldFromProperties($properties));
        }
    }

    /**
     * Add new fields to field array
     *
     * @return void
     */
    protected function addNewFields()
    {
        foreach ((array)$this->data[Field::TABLE_NAME] as $fieldUid => $properties) {
            $this->addField($this->makeFieldFromProperties($properties, $fieldUid));
        }
    }

    /**
     * Create Field from properties and uid
     *
     * Property "description" is used for the field uid
     * because the value could be an integer or a string
     * if it's new - like "new12abc"
     *
     * @param array $properties
     * @param int $uid
     * @return Field
     */
    protected function makeFieldFromProperties(array $properties, $uid = null)
    {
        /** @var Field $field */
        $field = $this->objectManager->get(Field::class);
        foreach ($properties as $key => $value) {
            $field->_setProperty($key, GeneralUtility::underscoredToLowerCamelCase($value));
        }
        if (!empty($properties['sys_language_uid'])) {
            $field->_setProperty('_languageUid', $properties['sys_language_uid']);
        }
        $field->setDescription((int)$properties['uid'] > 0 ? (int)$properties['uid'] : $uid);
        return $field;
    }

    /**
     * Get array with markers from a complete form
     *
     * @return array
     */
    protected function getFieldProperties()
    {
        $result = [];
        $select = 'f.*';
        $from = Form::TABLE_NAME . ' fo ' .
            'LEFT JOIN ' . Page::TABLE_NAME . ' p ON p.forms = fo.uid ' .
            'LEFT JOIN ' . Field::TABLE_NAME . ' f ON f.pages = p.uid';
        $where = 'fo.uid = ' . $this->getFormUid() . ' and f.deleted = 0';
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery($select, $from, $where, '', '', 1000);
        if ($res) {
            while (($row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res))) {
                $result[$row['uid']] = $row;
            }
        }

        return $result;
    }

    /**
     * Read Form Uid from GET params
     *
     * @return int form uid
     */
    protected function getFormUid()
    {
        $formUid = 0;

        // if form is given in GET params (open form and pages and fields via IRRE)
        if (isset($this->data[Form::TABLE_NAME])) {
            foreach (array_keys((array)$this->data[Form::TABLE_NAME]) as $uid) {
                $formUid = (int)$uid;
            }
        }

        // if pages open (fields via IRRE)
        if ($formUid === 0) {
            foreach (array_keys((array)$this->data[Page::TABLE_NAME]) as $uid) {
                if (!empty($this->data[Page::TABLE_NAME][$uid]['forms'])) {
                    $formUid = (int)$this->data[Page::TABLE_NAME][$uid]['forms'];
                }
            }
        }

        // if field is directly opened (no IRRE OR opened pages with their IRRE fields
        if ($formUid === 0) {
            foreach (array_keys((array)$this->data[Field::TABLE_NAME]) as $uid) {
                if (!empty($this->data[Field::TABLE_NAME][$uid]['pages'])) {
                    $formUid = $this->getFormUidFromRelatedPage($this->data[Field::TABLE_NAME][$uid]['pages']);
                }
            }
        }

        return $formUid;
    }

    /**
     * Get From Uid from related Page
     *
     * @param int $pageUid
     * @return int
     */
    protected function getFormUidFromRelatedPage($pageUid)
    {
        $formUid = 0;
        $select = 'fo.uid';
        $from = Form::TABLE_NAME . ' fo ' .
            'LEFT JOIN ' . Page::TABLE_NAME . ' p ON p.forms = fo.uid ' .
            'LEFT JOIN ' . Field::TABLE_NAME . ' f ON f.pages = p.uid';
        $where = 'p.uid = ' . (int)$pageUid;
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery($select, $from, $where, '', '', 1);
        if ($res) {
            $row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res);
            $formUid = (int)$row['uid'];
        }
        return $formUid;
    }

    /**
     * Add field to array (and may overwrite existing field from array)
     *
     * @param Field $field
     */
    protected function addField(Field $field)
    {
        if ($field->getDescription() && $field->getTitle()) {
            $this->fieldArray[$field->getDescription()] = $field;
        }
    }

    /**
     * Check if hook should do magic or not
     *
     * @param string $table
     * @return bool
     */
    protected function shouldProcess($table)
    {
        return in_array($table, $this->allowedTableNames);
    }

    /**
     * Check if a field should be manipulated
     *
     * @return bool
     */
    protected function shouldProcessField()
    {
        return isset($this->data[Field::TABLE_NAME][$this->uid]['marker']) || stristr($this->uid, 'NEW');
    }

    /**
     * @param array $markers
     * @return bool
     */
    protected function shouldRenameMarker(array $markers)
    {
        return !empty($markers[$this->uid]) && $markers[$this->uid] !== $this->properties['marker'];
    }
}
