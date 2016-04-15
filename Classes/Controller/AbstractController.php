<?php
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Abstract Controller for powermail
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractController extends ActionController
{
    use SignalTrait;

    /**
     * @var \In2code\Powermail\Domain\Repository\FormRepository
     * @inject
     */
    protected $formRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\FieldRepository
     * @inject
     */
    protected $fieldRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\AnswerRepository
     * @inject
     */
    protected $answerRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \In2code\Powermail\Domain\Service\UploadService
     * @inject
     */
    protected $uploadService;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * TypoScript configuration
     *
     * @var array
     */
    protected $conf;

    /**
     * Plugin variables
     *
     * @var array
     */
    protected $piVars;

    /**
     * message Class
     *
     * @var string
     */
    protected $messageClass = 'error';

    /**
     * selected page Uid
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Reformat array for createAction
     *
     * @return void
     */
    protected function reformatParamsForAction()
    {
        $this->uploadService->preflight($this->settings);
        $arguments = $this->request->getArguments();
        if (!isset($arguments['field'])) {
            return;
        }
        $newArguments = [
            'mail' => $arguments['mail']
        ];

        // allow subvalues in new property mapper
        $mailMvcArgument = $this->arguments->getArgument('mail');
        $propertyMapping = $mailMvcArgument->getPropertyMappingConfiguration();
        $propertyMapping->allowProperties('answers');
        $propertyMapping->allowCreationForSubProperty('answers');
        $propertyMapping->allowModificationForSubProperty('answers');
        $propertyMapping->allowProperties('form');
        $propertyMapping->allowCreationForSubProperty('form');
        $propertyMapping->allowModificationForSubProperty('form');

        // allow creation of new objects (for validation)
        $propertyMapping->setTypeConverterOptions(
            PersistentObjectConverter::class,
            [
                PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED => true,
                PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED => true
            ]
        );

        $iteration = 0;
        foreach ((array)$arguments['field'] as $marker => $value) {
            // ignore internal fields (honeypod)
            if (substr($marker, 0, 2) === '__') {
                continue;
            }
            $fieldUid = $this->fieldRepository->getFieldUidFromMarker($marker, $arguments['mail']['form']);
            // Skip fields without Uid (secondary password, upload)
            if ($fieldUid === 0) {
                continue;
            }

            // allow subvalues in new property mapper
            $propertyMapping->forProperty('answers')->allowProperties($iteration);
            $propertyMapping->forProperty('answers.' . $iteration)->allowAllProperties();
            $propertyMapping->allowCreationForSubProperty('answers.' . $iteration);
            $propertyMapping->allowModificationForSubProperty('answers.' . $iteration);

            /** @var Field $field */
            $field = $this->fieldRepository->findByUid($fieldUid);
            $valueType = $field->dataTypeFromFieldType(
                $this->fieldRepository->getFieldTypeFromMarker($marker, $arguments['mail']['form'])
            );
            if ($valueType === 3 && is_array($value)) {
                $value = $this->uploadService->getNewFileNamesByMarker($marker);
            }
            if (is_array($value)) {
                if (empty($value)) {
                    $value = '';
                } else {
                    $value = json_encode($value);
                }
            }
            $newArguments['mail']['answers'][$iteration] = [
                'field' => strval($fieldUid),
                'value' => $value,
                'valueType' => $valueType
            ];

            // edit form: add answer id
            if (!empty($arguments['field']['__identity'])) {
                $answer = $this->answerRepository->findByFieldAndMail($fieldUid, $arguments['field']['__identity']);
                if ($answer !== null) {
                    $newArguments['mail']['answers'][$iteration]['__identity'] = $answer->getUid();
                }
            }
            $iteration++;
        }

        // edit form: add mail id
        if (!empty($arguments['field']['__identity'])) {
            $newArguments['mail']['__identity'] = $arguments['field']['__identity'];
        }

        $this->request->setArguments($newArguments);
        $this->request->setArgument('field', null);
    }

    /**
     * Object initialization
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->piVars = $this->request->getArguments();
        $this->id = GeneralUtility::_GP('id');
    }

    /**
     * Deactivate errormessages in flashmessages
     *
     * @return bool
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }
}
