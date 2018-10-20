<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @return ObjectStorage|array
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

    /**
     * Get all fields to the current form
     *
     * @param string $fieldType "" => allFieldtypes OR $field::FIELD_TYPE_* => Field of this type
     * @return Field[]
     */
    public function getFields($fieldType = '')
    {
        $fields = [];
        foreach ($this->getPages() as $page) {
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
     * @param $fieldType
     * @return bool
     */
    protected function isCorrectFieldType(Field $field, $fieldType)
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
