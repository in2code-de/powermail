<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Page
 */
class Page extends AbstractEntity
{

    const TABLE_NAME = 'tx_powermail_domain_model_page';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $css = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Field>
     */
    protected $fields = null;

    /**
     * @var \In2code\Powermail\Domain\Model\Form
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $forms = null;

    /**
     * @var integer
     */
    protected $sorting = 0;

    /**
     * Container for fields with marker as key
     *
     * @var array
     */
    protected $fieldsByFieldMarker = [];

    /**
     * Container for fields with uid as key
     *
     * @var array
     */
    protected $fieldsByFieldUid = [];

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
            $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
            return $formRepository->findByPages($this->uid);
        }
        return $this->forms;
    }

    /**
     * Return fields as an array with markername as key.
     *
     *      Example to get a field title by markername {firstname} use:
     *          PHP: $page->getFieldsByFieldMarker()['firstname']->getTitle();
     *          FLUID: {page.fieldsByFieldMarker.firstname.title}
     *
     * @return array
     */
    public function getFieldsByFieldMarker()
    {
        if (empty($this->fieldsByFieldMarker)) {
            $fieldsArray = $this->getFields()->toArray();
            $this->fieldsByFieldMarker = array_combine(array_map(function (Field $field) {
                return $field->getMarker();
            }, $fieldsArray), $fieldsArray);
        }
        return $this->fieldsByFieldMarker;
    }

    /**
     * Return fields as an array with uid as key.
     *
     *      Example to get a field title by uid use:
     *          PHP: $page->getFieldsByFieldUid()[123]->getTitle();
     *          FLUID: {page.fieldsByFieldUid.123.title}
     *
     * @return array
     */
    public function getFieldsByFieldUid()
    {
        if (empty($this->fieldsByFieldUid)) {
            $fieldsArray = $this->getFields()->toArray();
            $this->fieldsByFieldUid = array_combine(array_map(function (Field $field) {
                return $field->getUid();
            }, $fieldsArray), $fieldsArray);
        }
        return $this->fieldsByFieldUid;
    }
}
