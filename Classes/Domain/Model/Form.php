<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Form
 */
class Form extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_form';

    /**
     * @var string
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
     * @return ObjectStorage|array
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
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
     * @param ObjectStorage $pages
     * @return void
     */
    public function setPages(ObjectStorage $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return bool
     * @throws Exception
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
     * @return array
     */
    public function getPagesByTitle(): array
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
    public function getPagesByUid(): array
    {
        if (empty($this->pagesByUid)) {
            $pagesArray = $this->getPages()->toArray();
            $this->pagesByUid = array_combine(array_map(function (Page $page) {
                return $page->getUid();
            }, $pagesArray), $pagesArray);
        }
        return $this->pagesByUid;
    }

    /**
     * Get all fields to the current form
     *
     * @param string $fieldType "" => allFieldtypes OR $field::FIELD_TYPE_* => Field of this type
     * @return Field[]
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
     * @param Field $field
     * @param string $fieldType
     * @return bool
     */
    protected function isCorrectFieldType(Field $field, string $fieldType): bool
    {
        if ($fieldType === '') {
            return true;
        } elseif ($fieldType === $field::FIELD_TYPE_BASIC) {
            return $field->isTypeOf($field::FIELD_TYPE_BASIC);
        } elseif ($fieldType === $field::FIELD_TYPE_ADVANCED) {
            return $field->isTypeOf($field::FIELD_TYPE_ADVANCED);
        } elseif ($fieldType === $field::FIELD_TYPE_EXTPORTABLE) {
            return $field->isTypeOf($field::FIELD_TYPE_EXTPORTABLE);
        }
        return false;
    }
}
