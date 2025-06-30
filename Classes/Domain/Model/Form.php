<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Form
 */
class Form extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_form';

    protected string $title = '';

    protected string $css = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Page>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $pages;

    /**
     * Container for pages with title as key
     */
    protected array $pagesByTitle = [];

    /**
     * Container for pages with uid as key
     */
    protected array $pagesByUid = [];

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

    /**
     * @return ObjectStorage|array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getPages()
    {
        // if elementbrowser instead of IRRE (sorting workarround)
        if (ConfigurationUtility::isReplaceIrreWithElementBrowserActive()) {
            $formRepository = GeneralUtility::makeInstance(FormRepository::class);
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

    public function setPages(ObjectStorage $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @throws DeprecatedException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function hasUploadField(): bool
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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getPagesByTitle(): array
    {
        if ($this->pagesByTitle === []) {
            $pagesArray = $this->getPages()->toArray();
            $this->pagesByTitle = array_combine(array_map(fn (Page $page): string => StringUtility::cleanString($page->getTitle()), $pagesArray), $pagesArray);
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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function getPagesByUid(): array
    {
        if ($this->pagesByUid === []) {
            $pagesArray = $this->getPages()->toArray();
            $this->pagesByUid = array_combine(array_map(fn (Page $page): ?int => $page->getUid(), $pagesArray), $pagesArray);
        }

        return $this->pagesByUid;
    }

    /**
     * Get all fields to the current form
     *
     * @param string $fieldType "" => allFieldtypes OR $field::FIELD_TYPE_* => Field of this type
     * @return Field[]
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws DeprecatedException
     */
    public function getFields(string $fieldType = ''): array
    {
        $fields = [];
        foreach ($this->getPages() as $page) {
            /** @var Field $field */
            foreach ($page->getFields() as $field) {
                if ($this->isCorrectFieldType($field, $fieldType)) {
                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * @throws DeprecatedException
     */
    protected function isCorrectFieldType(Field $field, string $fieldType): bool
    {
        if ($fieldType === '') {
            return true;
        }

        if ($fieldType === $field::FIELD_TYPE_BASIC) {
            return $field->isTypeOf($field::FIELD_TYPE_BASIC);
        }

        if ($fieldType === $field::FIELD_TYPE_ADVANCED) {
            return $field->isTypeOf($field::FIELD_TYPE_ADVANCED);
        }

        if ($fieldType === $field::FIELD_TYPE_EXTPORTABLE) {
            return $field->isTypeOf($field::FIELD_TYPE_EXTPORTABLE);
        }

        return false;
    }
}
