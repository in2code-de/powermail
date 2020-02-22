<?php
declare(strict_types=1);
namespace In2code\Powermail\Hook;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use In2code\Powermail\Utility\DatabaseUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CreateMarker to autofill field marker with value from title e.g. {firstname}
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
     * @throws Exception
     */
    public function initialize(string $status, string $table, string $uid, array &$properties): void
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
     * @throws Exception
     */
    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        string $uid,
        array &$properties
    ): void {
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
    protected function setMarkerForFields(array $markers): void
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
    protected function renameMarker(array $markers): void
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
    protected function cleanMarkersInLocalizedFields(): void
    {
        if (!empty($this->properties['sys_language_uid']) && $this->properties['sys_language_uid'] > 0 &&
            !empty($this->properties['l10n_parent']) && $this->properties['l10n_parent'] > 0) {
            $this->properties['marker'] = '';
        }
    }

    /**
     * Check if persisted fields should have a different marker name and rename it if it's necessary
     *
     * @param array $markers
     * @return void
     */
    protected function checkAndRenameMarkers(array $markers): void
    {
        foreach ($markers as $uid => $marker) {
            $row = BackendUtilityCore::getRecord(
                Field::TABLE_NAME,
                (int)$uid,
                'marker'
            );
            if ($row['marker'] !== $marker) {
                $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Field::TABLE_NAME);
                $queryBuilder->update(Field::TABLE_NAME)->where('uid=' . (int)$uid)->set('marker', $marker)->execute();
            }
        }
    }

    /**
     * @param $marker
     * @return void
     */
    protected function setMarkerProperty(string $marker): void
    {
        $this->properties['marker'] = $marker;
    }

    /**
     * Add existing fields from database to field array
     *
     * @return void
     * @throws Exception
     */
    protected function addExistingFields(): void
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
     * @throws Exception
     */
    protected function addNewFields(): void
    {
        foreach ((array)$this->data[Field::TABLE_NAME] as $fieldUid => $properties) {
            $this->addField($this->makeFieldFromProperties($properties, (string)$fieldUid));
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
     * @param string $uid Number for persisted and string for new fields like "NEW5e2d7c8f48f4a868804329"
     * @return Field
     * @throws Exception
     */
    protected function makeFieldFromProperties(array $properties, string $uid = '0')
    {
        /** @var Field $field */
        $field = $this->objectManager->get(Field::class);
        foreach ($properties as $key => $value) {
            $field->_setProperty(GeneralUtility::underscoredToLowerCamelCase($key), $value);
        }
        if (!empty($properties['sys_language_uid'])) {
            $field->_setProperty('_languageUid', $properties['sys_language_uid']);
        }
        $field->setDescription((string)$properties['uid'] > 0 ? (string)$properties['uid'] : $uid);
        return $field;
    }

    /**
     * Get array with markers from a complete form
     *
     * @return array
     */
    protected function getFieldProperties(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        return $queryBuilder
            ->select('f.*')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.form = fo.uid')
            ->join('p', Field::TABLE_NAME, 'f', 'f.page = p.uid')
            ->where('fo.uid = ' . $this->getFormUid() . ' and f.deleted = 0')
            ->setMaxResults(1000)
            ->execute()
            ->fetchAll();
    }

    /**
     * Read Form Uid from GET params
     *
     * @return int form uid
     */
    protected function getFormUid(): int
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
                if (!empty($this->data[Page::TABLE_NAME][$uid]['form'])) {
                    $formUid = (int)$this->data[Page::TABLE_NAME][$uid]['form'];
                }
            }
        }

        // if field is directly opened (no IRRE OR opened pages with their IRRE fields
        if ($formUid === 0) {
            foreach (array_keys((array)$this->data[Field::TABLE_NAME]) as $uid) {
                if (!empty($this->data[Field::TABLE_NAME][$uid]['page'])) {
                    $formUid = $this->getFormUidFromRelatedPage((int)$this->data[Field::TABLE_NAME][$uid]['page']);
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
    protected function getFormUidFromRelatedPage(int $pageUid): int
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Form::TABLE_NAME, true);
        return (int)$queryBuilder
            ->select('fo.uid')
            ->from(Form::TABLE_NAME, 'fo')
            ->join('fo', Page::TABLE_NAME, 'p', 'p.form = fo.uid')
            ->where('p.uid = ' . (int)$pageUid)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Add field to array (and may overwrite existing field from array)
     *
     * @param Field $field
     * @return void
     */
    protected function addField(Field $field): void
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
    protected function shouldProcess(string $table): bool
    {
        return in_array($table, $this->allowedTableNames);
    }

    /**
     * Check if a field should be manipulated
     *
     * @return bool
     */
    protected function shouldProcessField(): bool
    {
        return isset($this->data[Field::TABLE_NAME][$this->uid]['marker']) || stristr((string)$this->uid, 'NEW');
    }

    /**
     * @param array $markers
     * @return bool
     */
    protected function shouldRenameMarker(array $markers): bool
    {
        return !empty($markers[$this->uid]) && $markers[$this->uid] !== $this->properties['marker'];
    }
}
