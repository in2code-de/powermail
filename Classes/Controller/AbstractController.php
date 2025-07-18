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
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\StringUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractController
 */
abstract class AbstractController extends ActionController
{
    /**
     * TypoScript configuration
     */
    protected array $conf;

    /**
     * Plugin variables
     */
    protected array $piVars;

    /**
     * message Class
     */
    protected string $messageClass = 'error';

    /**
     * selected page Uid
     */
    protected int $id = 0;

    protected ContentObjectRenderer $contentObject;

    protected bool $isPhpSpreadsheetInstalled = false;

    public function __construct(
        protected FormRepository $formRepository,
        protected FieldRepository $fieldRepository,
        protected MailRepository $mailRepository,
        protected UploadService $uploadService,
        protected EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * Make $this->settings accessible when extending the controller with events
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Make $this->settings writable when extending the controller with events
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Reformat array for createAction
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws NoSuchArgumentException
     * @throws ExceptionExtbaseObject
     * @throws DeprecatedException
     */
    protected function reformatParamsForAction(): void
    {
        $this->uploadService->preflight($this->settings);
        $arguments = array_merge_recursive($this->request->getArguments(), $this->request->getUploadedFiles());
        if (!isset($arguments['field'])) {
            return;
        }

        $newArguments = [
            'mail' => $arguments['mail'],
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
                PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED => true,
            ]
        );

        $iteration = 0;
        foreach ((array)$arguments['field'] as $marker => $value) {
            // ignore internal fields (honeypod)
            if (StringUtility::startsWith((string)$marker, '__')) {
                continue;
            }

            $fieldUid = $this->fieldRepository->getFieldUidFromMarker($marker, (int)$arguments['mail']['form']);
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
                $this->fieldRepository->getFieldTypeFromMarker($marker, (int)$arguments['mail']['form'])
            );
            if ($valueType === Answer::VALUE_TYPE_UPLOAD && is_array($value)) {
                $value = $this->uploadService->getNewFileNamesByMarker($marker);
            }

            if (is_array($value)) {
                $value = $value === [] ? '' : json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $newArguments['mail']['answers'][$iteration] = [
                'field' => (string)$fieldUid,
                'value' => $value,
                'valueType' => $valueType,
            ];

            // edit form: add answer id
            if (!empty($arguments['field']['__identity'])) {
                $answerRepository = GeneralUtility::makeInstance(AnswerRepository::class);
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

        $this->request = $this->request->withArguments($newArguments);
        $this->request = $this->request->withArgument('field', null);
    }

    /**
     * Object initialization
     */
    protected function initializeAction(): void
    {
        $this->piVars = $this->request->getArguments();
        $this->id = (int)($this->request->getParsedBody()['id'] ?? $this->request->getQueryParams()['id'] ?? null);
    }

    /**
     * Deactivate errormessages in flashmessages
     */
    protected function getErrorFlashMessage(): bool
    {
        return false;
    }
}
