<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Factory;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileFactory
 */
class FileFactory
{

    /**
     * @var array
     */
    protected $settings = [];

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
     * @param string $key
     * @return File|null
     */
    public function getInstanceFromFilesArray(array $filesArray, $marker, $key)
    {
        $originalName = $filesArray['name']['field'][$marker][$key];
        $size = $filesArray['size']['field'][$marker][$key];
        $type = $filesArray['type']['field'][$marker][$key];
        $temporaryName = $filesArray['tmp_name']['field'][$marker][$key];
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
     */
    public function getInstanceFromUploadArguments($marker, $value, array $arguments)
    {
        /** @var FieldRepository $fieldRepository */
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $field = $fieldRepository->findByMarkerAndForm($marker, (int)$arguments['mail']['form']);
        if ($field !== null && $field->dataTypeFromFieldType($field->getType()) === 3 && !empty($value)) {
            return $this->makeFileInstance($marker, $value, null, null, null, true);
        }
        return null;
    }

    /**
     * Get instance of File from existing answer
     *
     * @param string $fileName
     * @param Answer $answer
     * @return File|null
     */
    public function getInstanceFromExistingAnswerValue($fileName, Answer $answer)
    {
        $form = $answer->getField()->getPages()->getForms();
        $marker = $answer->getField()->getMarker();
        return $this->makeFileInstance($marker, $fileName, null, null, null, true, $form);
    }

    /**
     * Get File instance
     *
     * @param string $marker
     * @param string $originalName
     * @param int $size
     * @param string $type
     * @param string $temporaryName
     * @param bool $uploaded
     * @param Form $form
     * @return File
     */
    protected function makeFileInstance(
        $marker,
        $originalName,
        $size = null,
        $type = null,
        $temporaryName = null,
        $uploaded = false,
        Form $form = null
    ) {
        /** @var File $file */
        $file = ObjectUtility::getObjectManager()->get(File::class, $marker, $originalName, $temporaryName);
        $file->setNewName(StringUtility::cleanString($originalName));
        $file->setUploadFolder($this->getUploadFolder());
        if ($size === null) {
            $size = filesize($file->getNewPathAndFilename(true));
        }
        $file->setSize($size);
        if ($type === null) {
            $type = mime_content_type($file->getNewPathAndFilename(true));
        }
        $file->setType($type);
        $file->setUploaded($uploaded);

        /* @var FieldRepository $fieldRepository */
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $file->setField($fieldRepository->findByMarkerAndForm($marker, $this->getFormUid($form)));
        return $file;
    }

    /**
     * @return string
     */
    protected function getUploadFolder()
    {
        return $this->settings['misc']['file']['folder'];
    }

    /**
     * @param Form|null $form
     * @return int
     */
    protected function getFormUid(Form $form = null)
    {
        if ($form === null) {
            $arguments = $this->getArguments();
            return (int)$arguments['mail']['form'];
        } else {
            return $form->getUid();
        }
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return (array)GeneralUtility::_GP('tx_powermail_pi1');
    }
}
