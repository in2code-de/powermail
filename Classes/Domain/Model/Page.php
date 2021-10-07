<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\Exception;
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
     */
    protected $form = null;

    /**
     * @var int
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
     * @return void
     */
    protected function initStorageObjects(): void
    {
        $this->fields = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCss(): string
    {
        return $this->css;
    }

    /**
     * @param string $css
     * @return void
     */
    public function setCss(string $css): void
    {
        $this->css = $css;
    }

    /**
     * @param Field $field
     * @return void
     */
    public function addField(Field $field): void
    {
        $this->fields->attach($field);
    }

    /**
     * @param Field $fieldToRemove
     * @return void
     */
    public function removeField(Field $fieldToRemove): void
    {
        $this->fields->detach($fieldToRemove);
    }

    /**
     * @return ObjectStorage
     */
    public function getFields(): ObjectStorage
    {
        return $this->fields;
    }

    /**
     * @param ObjectStorage $fields
     * @return void
     */
    public function setFields(ObjectStorage $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     * @return void
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @param Form $form
     * @return void
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return Form
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getForm(): Form
    {
        $form = $this->form;
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
            $form = $formRepository->findByPages($this->uid);
        }
        return $form;
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
    public function getFieldsByFieldMarker(): array
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
    public function getFieldsByFieldUid(): array
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
