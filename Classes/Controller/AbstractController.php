<?php
declare(strict_types=1);
namespace In2code\Powermail\Controller;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Repository\AnswerRepository;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Service\UploadService;
use In2code\Powermail\Signal\SignalTrait;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractController
 */
abstract class AbstractController extends ActionController
{
    use SignalTrait;

    /**
     * @var FormRepository
     */
    protected $formRepository;

    /**
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var MailRepository
     */
    protected $mailRepository;

    /**
     * @var UploadService
     */
    protected $uploadService;

    /**
     * @var ContentObjectRenderer
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
     * Make $this->settings accessable when extending the controller with signals
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Reformat array for createAction
     *
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
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
            if (StringUtility::startsWith((string)$marker, '__')) {
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
            if ($valueType === Answer::VALUE_TYPE_UPLOAD && is_array($value)) {
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
                $answerRepository = $this->objectManager->get(AnswerRepository::class);
                $answer = $answerRepository->findByFieldAndMail($fieldUid, $arguments['field']['__identity']);
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
     * @param FormRepository $formRepository
     * @return void
     */
    public function injectFormRepository(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    /**
     * @param FieldRepository $fieldRepository
     * @return void
     */
    public function injectFieldRepository(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * @param MailRepository $mailRepository
     * @return void
     */
    public function injectMailRepository(MailRepository $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    /**
     * @param UploadService $uploadService
     * @return void
     */
    public function injectUploadService(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
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
