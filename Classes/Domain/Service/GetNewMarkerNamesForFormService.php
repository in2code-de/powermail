<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Page;

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
 * Class GetNewMarkerNamesForFormService
 * @package In2code\Powermail\Domain\Service
 */
class GetNewMarkerNamesForFormService
{

    /**
     * @var \In2code\Powermail\Domain\Repository\FormRepository
     * @inject
     */
    protected $formRepository;

    /**
     * Restricted Marker Names
     *
     * @var array
     */
    protected $restrictedMarkers = [
        'mail',
        'powermail_rte',
        'powermail_all'
    ];

    /**
     * @var string
     */
    protected $defaultMarker = 'marker';

    /**
     * @var int
     */
    protected $iterations = 99;

    /**
     * Get array with formUids and fieldUids and their new marker names
     *
     *  [
     *      123 =>
     *          [
     *              12 => 'newmarkername',
     *              13 => 'newmarkername_01',
     *          ]
     *  ]
     *
     * @param int $formUid
     * @param bool $forceReset
     * @return array
     */
    public function getMarkersForFieldsDependingOnForm($formUid, $forceReset)
    {
        if ($formUid === 0) {
            $forms = $this->formRepository->findAll();
        } else {
            $forms = [$this->formRepository->findByUid($formUid)];
        }
        $markers = [];
        /** @var Form $form */
        foreach ($forms as $form) {
            $markers[$form->getUid()] = $this->makeUniqueValueInArray($this->getFieldsToForm($form), $forceReset);
        }
        return $markers;
    }

    /**
     * Get all fields to a form
     *
     * @param Form $form
     * @return array
     */
    protected function getFieldsToForm(Form $form)
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
     * @param array $fieldArray
     * @param bool $forceReset
     * @return array
     */
    protected function makeUniqueValueInArray(array $fieldArray, $forceReset)
    {
        $markerArray = [];
        /** @var Field $field */
        foreach ($fieldArray as $field) {
            $marker = $this->fallbackMarkerIfEmpty($field, $forceReset);
            if ($this->isMarkerAllowed($marker, $markerArray)) {
                $markerArray[$field->getUid()] = $marker;
            } else {
                for ($i = 1; $i < $this->iterations; $i++) {
                    $marker = $this->removeAppendix($marker);
                    $marker = $this->addAppendix($marker, $i);
                    if (!in_array($marker, $markerArray)) {
                        $markerArray[$field->getUid()] = $marker;
                        break;
                    }
                }
            }
        }
        return $markerArray;
    }

    /**
     * @param Field $field
     * @param bool $forceReset
     * @return string
     */
    protected function fallbackMarkerIfEmpty(Field $field, $forceReset)
    {
        $marker = $field->getMarkerOriginal();
        if (empty($marker) || $forceReset) {
            $marker = $this->cleanString($field->getTitle());
        }
        if (empty($marker)) {
            $marker = $this->defaultMarker;
        }
        return $marker;
    }

    /**
     * remove appendix "_xx"
     *
     * @param string $string
     * @return string
     */
    protected function removeAppendix($string)
    {
        $part = '[0-9]';
        $pattern = '';
        for ($i = 0; $i < strlen($this->iterations); $i++) {
            $pattern .= $part;
        }
        return $string = preg_replace('/_' . $pattern . '$/', '', $string);
    }

    /**
     * add appendix "_xx"
     *
     * @param string $string
     * @param int $iteration
     * @return string
     */
    protected function addAppendix($string, $iteration)
    {
        $string .= '_' . str_pad($iteration, strlen($this->iterations), '0', STR_PAD_LEFT);
        return $string;
    }

    /**
     * @param string $marker
     * @param array $newArray
     * @return bool
     */
    protected function isMarkerAllowed($marker, array $newArray)
    {
        return !in_array($marker, $newArray) && !in_array($marker, $this->restrictedMarkers);
    }

    /**
     * Clean Marker String
     *        "My Field ?1$2§3" => "myfield123"
     *
     * @param string $string Any String
     * @return string
     */
    protected function cleanString($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
        $string = str_replace('-', '_', $string);
        $string = strtolower($string);
        return $string;
    }
}
