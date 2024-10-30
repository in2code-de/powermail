<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;

/**
 * Class GetNewMarkerNamesForFormService
 */
class GetNewMarkerNamesForFormService
{
    protected static string $defaultMarker = 'marker';

    /**
     * Restricted Marker Names
     */
    protected array $restrictedMarkers = [
        'mail',
        'powermail_rte',
        'powermail_all',
    ];

    protected int $iterations = 99;

    /**
     * Get array with formUids and fieldUids and their new marker names
     *
     *  [
     *      123 => [
     *          12 => 'newmarkername',
     *          13 => 'newmarkername_01',
     *      ]
     *  ]
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExceptionExtbaseObject
     */
    public function getMarkersForFieldsDependingOnForm(int $formUid, bool $forceReset): array
    {
        $forms = [];
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        if ($formUid === 0) {
            $forms = $formRepository->findAll();
        } elseif ($form = $formRepository->findByUid($formUid)) {
            $forms = [$form];
        }

        $markers = [];
        /** @var Form $form */
        foreach ($forms as $form) {
            $markers[$form->getUid()] = $this->makeUniqueValueInArray($this->getFieldsToForm($form), $forceReset);
        }

        return $markers;
    }

    /**
     * create marker array with unique values
     *
     *  [
     *      Field [123]
     *      Field [234]
     *  ]
     *
     *      =>
     *
     *  [
     *      123 => 'markername1',
     *      234 => 'markername2'
     *  ]
     *
     * @throws ExceptionExtbaseObject
     */
    public function makeUniqueValueInArray(array $fieldArray, bool $forceReset = false): array
    {
        $markerArray = [];
        /** @var Field $field */
        foreach ($fieldArray as $field) {
            $marker = $this->fallbackMarkerIfEmpty($field, $forceReset);
            $uid = $this->getUid($field);
            if ($field->isLocalized()) {
                $markerArray[$uid] = '';
            } else {
                $marker = $this->dontAllowNumbersOnly($marker);
                if ($this->isMarkerAllowed($marker, $markerArray)) {
                    $markerArray[$uid] = $marker;
                } else {
                    for ($i = 1; $i < $this->iterations; $i++) {
                        $marker = $this->removeAppendix($marker);
                        $marker = $this->addAppendix($marker, $i);
                        if (!in_array($marker, $markerArray)) {
                            $markerArray[$uid] = $marker;
                            break;
                        }
                    }
                }
            }
        }

        return $markerArray;
    }

    /**
     * Get all fields to a form
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function getFieldsToForm(Form $form): array
    {
        $fields = [];
        foreach ($form->getPages() as $page) {
            /** @var Page $page */
            foreach ($page->getFields() as $field) {
                /** @var Field $field */
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Get uid from field
     *
     * because new fields that are just generated in TYPO3 backend get unique string names like
     * "new123abc" before they are persisted in database. It could be that, they are temporary
     * stored in field description. In this case the uid is always empty
     *
     * @return int|string
     */
    protected function getUid(Field $field)
    {
        $uid = 0;
        if ($field->getUid()) {
            $uid = $field->getUid();
        } elseif ($field->getDescription() !== '' && $field->getDescription() !== '0') {
            $uid = $field->getDescription();
        }

        return $uid;
    }

    /**
     * @throws ExceptionExtbaseObject
     */
    protected function fallbackMarkerIfEmpty(Field $field, bool $forceReset): string
    {
        $marker = $field->getMarkerOriginal();
        if ($marker === '' || $marker === '0' || $forceReset) {
            $marker = $this->cleanString($field->getTitle());
        }

        if ($marker === '' || $marker === '0') {
            return self::$defaultMarker;
        }

        return $marker;
    }

    /**
     * remove appendix "_xx"
     */
    protected function removeAppendix(string $string): string
    {
        $part = '[0-9]';
        $pattern = '';
        for ($i = 0; $i < strlen((string)$this->iterations); $i++) {
            $pattern .= $part;
        }

        return $string = preg_replace('/_' . $pattern . '$/', '', $string);
    }

    /**
     * add appendix "_xx"
     */
    protected function addAppendix(string $string, int $iteration): string
    {
        return $string . ('_' . str_pad((string)$iteration, strlen((string)$this->iterations), '0', STR_PAD_LEFT));
    }

    /**
     * add appendix "marker" if marker is only a number
     */
    protected function dontAllowNumbersOnly(string $marker): string
    {
        if (is_numeric($marker)) {
            return 'marker' . $marker;
        }

        return $marker;
    }

    protected function isMarkerAllowed(string $marker, array $newArray): bool
    {
        return !in_array($marker, $newArray) && !in_array($marker, $this->restrictedMarkers);
    }

    /**
     * Clean Marker String
     *        "My Field ?1$2§3" => "myfield123"
     *
     * @param string $string Any String
     */
    protected function cleanString(string $string): string
    {
        $csConverter = GeneralUtility::makeInstance(CharsetConverter::class);
        $string = $csConverter->specCharsToASCII('utf-8', $string);
        $string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
        $string = str_replace('-', '_', $string);
        return strtolower($string);
    }

    public static function getRandomMarkerName(): string
    {
        return self::$defaultMarker . '_' . StringUtility::getRandomString(8, false);
    }
}
