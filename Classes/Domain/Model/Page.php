<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Page
 */
class Page extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_page';

    protected string $title = '';

    protected string $css = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Field>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $fields;

    protected ?Form $form = null;

    protected int $sorting = 0;

    /**
     * Container for fields with marker as key
     */
    protected array $fieldsByFieldMarker = [];

    /**
     * Container for fields with uid as key
     */
    protected array $fieldsByFieldUid = [];

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initializeObject();
    }

    protected function initializeObject(): void
    {
        $this->fields = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCss(): string
    {
        return $this->css;
    }

    public function setCss(string $css): void
    {
        $this->css = $css;
    }

    public function addField(Field $field): void
    {
        $this->fields->attach($field);
    }

    public function removeField(Field $fieldToRemove): void
    {
        $this->fields->detach($fieldToRemove);
    }

    public function getFields(): ObjectStorage
    {
        return $this->fields;
    }

    public function setFields(ObjectStorage $fields): void
    {
        $this->fields = $fields;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getForm(): Form
    {
        $form = $this->form;
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            $formRepository = GeneralUtility::makeInstance(FormRepository::class);
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
     */
    public function getFieldsByFieldMarker(): array
    {
        if ($this->fieldsByFieldMarker === []) {
            $fieldsArray = $this->getFields()->toArray();
            $this->fieldsByFieldMarker = array_combine(array_map(fn (Field $field): string => $field->getMarker(), $fieldsArray), $fieldsArray);
        }

        return $this->fieldsByFieldMarker;
    }

    /**
     * Return fields as an array with uid as key.
     *
     *      Example to get a field title by uid use:
     *          PHP: $page->getFieldsByFieldUid()[123]->getTitle();
     *          FLUID: {page.fieldsByFieldUid.123.title}
     */
    public function getFieldsByFieldUid(): array
    {
        if ($this->fieldsByFieldUid === []) {
            $fieldsArray = $this->getFields()->toArray();
            $this->fieldsByFieldUid = array_combine(array_map(fn (Field $field): ?int => $field->getUid(), $fieldsArray), $fieldsArray);
        }

        return $this->fieldsByFieldUid;
    }
}
