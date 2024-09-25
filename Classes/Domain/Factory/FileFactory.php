<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Factory;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Exception\DeprecatedException;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Type\File\FileInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class FileFactory
 */
class FileFactory
{
    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get instance of File from Files Array
     *
     * @param array $filesArray normally $_FILES['tx_powermail_pi1']
     * @param string $marker
     * @param int $key
     * @return File|null
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     */
    public function getInstanceFromFilesArray(array $filesArray, string $marker, int $key): ?File
    {
        $originalName = $filesArray['name']['field'][$marker][$key] ?? '';
        $size = $filesArray['size']['field'][$marker][$key] ?? 0;
        $type = $filesArray['type']['field'][$marker][$key] ?? '';
        $temporaryName = $filesArray['tmp_name']['field'][$marker][$key] ?? '';
        if (!empty($originalName) && !empty($temporaryName) && $size > 0) {
            return $this->makeFileInstance($marker, $originalName, $size, $type, $temporaryName);
        }
        return null;
    }

    /**
     * Get instance of File from arguments
     *
     * @param string $marker
     * @param string $value
     * @param array $arguments
     * @return File|null
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws DeprecatedException
     * @throws ExceptionExtbaseObject
     */
    public function getInstanceFromUploadArguments(string $marker, string $value, array $arguments): ?File
    {
        $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
        $field = $fieldRepository->findByMarkerAndForm($marker, (int)$arguments['mail']['form']);
        if ($field !== null && $field->dataTypeFromFieldType($field->getType()) === 3 && !empty($value)) {
            return $this->makeFileInstance($marker, $value, 0, '', '', true);
        }
        return null;
    }

    /**
     * Get instance of File from existing answer
     *
     * @param string $fileName
     * @param Answer $answer
     * @return File
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     */
    public function getInstanceFromExistingAnswerValue(string $fileName, Answer $answer): File
    {
        $form = $answer->getField()->getPage()->getForm();
        $marker = $answer->getField()->getMarker();
        return $this->makeFileInstance($marker, $fileName, 0, '', '', true, $form);
    }

    /**
     * This subfunction is used to create a file instance. E.g. when a file was just uploaded or when a confirmation
     * page is active, when a file was already uploaded in the step before.
     *
     * @param string $marker
     * @param string $originalName
     * @param int $size
     * @param string $type
     * @param string $temporaryName
     * @param bool $uploaded
     * @param ?Form $form
     * @return File
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws ExceptionExtbaseObject
     */
    protected function makeFileInstance(
        string $marker,
        string $originalName,
        int $size = 0,
        string $type = '',
        string $temporaryName = '',
        bool $uploaded = false,
        Form $form = null
    ): File {
        $file = GeneralUtility::makeInstance(File::class, $marker, $originalName, $temporaryName);
        $file->setNewName(StringUtility::cleanString($originalName));
        $file->setUploadFolder($this->getUploadFolder());
        if ($size === 0) {
            $size = (int)filesize($file->getNewPathAndFilename(true));
        }
        $file->setSize($size);
        if ($type === '') {
            $type = (new FileInfo($file->getTemporaryName()))->getMimeType() ?: 'application/octet-stream';
        }
        $file->setType($type);
        $file->setUploaded($uploaded);

        /* @var FieldRepository $fieldRepository */
        $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
        $file->setField($fieldRepository->findByMarkerAndForm($marker, $this->getFormUid($form)));
        return $file;
    }

    /**
     * @return string
     */
    protected function getUploadFolder(): string
    {
        return $this->settings['misc']['file']['folder'];
    }

    /**
     * @param ?Form $form
     * @return int
     */
    protected function getFormUid(Form $form = null): int
    {
        if ($form === null) {
            $arguments = FrontendUtility::getArguments(FrontendUtility::getPluginName());
            return (int)$arguments['mail']['form'];
        }
        return $form->getUid();
    }
}
