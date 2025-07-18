<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use Exception;
use In2code\Powermail\Domain\Factory\FileFactory;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\File;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Events\UploadServiceGetFilesEvent;
use In2code\Powermail\Events\UploadServicePreflightEvent;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\FrontendUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class UploadService
 */
class UploadService implements SingletonInterface
{
    /**
     * Contains all files from upload
     *
     * @var File[]
     */
    protected array $files = [];

    /**
     * Temporary filenames array to find duplicates
     */
    protected array $fileNames = [];

    protected array $settings = [];

    private readonly EventDispatcherInterface $eventDispatcher;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     * @throws Exception
     */
    public function preflight(array $settings): void
    {
        $this->settings = $settings;
        $this->fillFilesFromFilesArray();
        $this->fillFilesFromHiddenFields();
        $this->fillFilesFromExistingMail();
        $this->makeUniqueFilenames();
        $this->eventDispatcher->dispatch(new UploadServicePreflightEvent($this));
    }

    /**
     * Upload all files to upload folder
     *
     * @return bool true if files where uploaded correctly
     * @throws Exception
     */
    public function uploadAllFiles(): bool
    {
        $result = false;
        foreach ($this->getFiles() as $file) {
            if (!$file->isUploaded() && $file->isValid() && $this->isFileExtensionAllowed($file, $this->getAllowedExtensions())) {
                BasicFileUtility::createFolderIfNotExists($file->getUploadFolder());
                if (GeneralUtility::upload_copy_move($file->getTemporaryName(), $file->getNewPathAndFilename())) {
                    $file->setUploaded(true);
                    $result = true;
                } else {
                    return false;
                }
            }
        }

        return $result;
    }

    /**
     * Get all new filenames by given marker (to show filenames on confirmation page again, etc...)
     * If empty, use values from arguments
     *
     * @throws Exception
     */
    public function getNewFileNamesByMarker(string $marker): array
    {
        $newFileNames = [];
        foreach ($this->getFiles() as $file) {
            if ($file->getMarker() === $marker) {
                $newFileNames[] = $file->getNewName();
            }
        }

        return $newFileNames;
    }

    /**
     * @param string $fileExtensions allowed file extensions as commaseparated list
     */
    public function isFileExtensionAllowed(File $file, string $fileExtensions = ''): bool
    {
        $filename = $file->getOriginalName();
        $fileInfo = pathinfo($filename);
        $extension = strtolower($fileInfo['extension']);
        return $extension !== '' && $extension !== '0' &&
            ($fileExtensions !== '' && $fileExtensions !== '0') &&
            GeneralUtility::inList($fileExtensions, $extension) &&
            GeneralUtility::makeInstance(FileNameValidator::class)->isValid($filename) &&
            GeneralUtility::validPathStr($filename);
    }

    public function isFileSizeSmallerThenAllowed(File $file, int $maximumSize): bool
    {
        return $file->getSize() <= $maximumSize;
    }

    /**
     * Prepares files from $_FILES array to $this->files
     * This will be used by the first submit (before confirmation page will be submitted)
     *
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    protected function fillFilesFromFilesArray(): void
    {
        $filesArrayPowermail = ObjectUtility::getFilesArray();
        if ($filesArrayPowermail !== []) {
            $filesArray = (array)$filesArrayPowermail[FrontendUtility::getPluginName()];
            foreach ((array)$filesArray['name']['field'] as $marker => $files) {
                foreach (array_keys($files) as $key) {
                    /** @var FileFactory $fileFactory */
                    $fileFactory = GeneralUtility::makeInstance(FileFactory::class, $this->settings);
                    $file = $fileFactory->getInstanceFromFilesArray($filesArray, $marker, $key);
                    if ($file !== null) {
                        $this->addFile($file);
                    }
                }
            }
        }
    }

    /**
     * Fill files from hidden field values to $this->files only if same marker from $_FILES is empty
     * This will happen, if a confirmation page is in use and file values are no more stored in $_FILES per default
     *
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    protected function fillFilesFromHiddenFields(): void
    {
        $arguments = $this->getArguments();
        foreach ((array)($arguments['field'] ?? []) as $marker => $values) {
            $fileNames = $this->getNewFileNamesByMarker($marker);
            if ($fileNames === []) {
                foreach ((array)$values as $value) {
                    /** @var FileFactory $fileFactory */
                    $fileFactory = GeneralUtility::makeInstance(FileFactory::class, $this->settings);
                    $file = $fileFactory->getInstanceFromUploadArguments($marker, $value, $arguments);
                    if ($file !== null) {
                        $this->addFile($file);
                    }
                }
            }
        }
    }

    /**
     * Fill files from existing mail object. Mail was saved some times before but is hidden (normal functionality if
     * optin is activated in powermail). So try to search for uploaded files from given values in answers.
     *
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidQueryException
     */
    protected function fillFilesFromExistingMail(): void
    {
        $arguments = $this->getArguments();
        if ($this->isOptinConfirmWithExistingMail($arguments)) {
            $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
            /** @var Mail $mail */
            $mail = $mailRepository->findByUid((int)$arguments['mail']);
            if ($mail !== null) {
                $answers = $mail->getAnswersByValueType(Answer::VALUE_TYPE_UPLOAD);
                foreach ($answers as $answer) {
                    /** @var FileFactory $fileFactory */
                    $fileFactory = GeneralUtility::makeInstance(FileFactory::class, $this->settings);
                    $value = $answer->getValue();
                    if (is_array($value)) {
                        foreach ($value as $valueItem) {
                            $file = $fileFactory->getInstanceFromExistingAnswerValue($valueItem, $answer);
                            if ($file !== null) {
                                $this->addFile($file);
                            }
                        }
                    } else {
                        $file = $fileFactory->getInstanceFromExistingAnswerValue($value, $answer);
                        if ($file !== null) {
                            $this->addFile($file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if given filenames are unique and does not exist in target folder
     * Rename filenames if needed
     *
     * @throws Exception
     */
    protected function makeUniqueFilenames(): void
    {
        foreach ($this->getFiles() as $file) {
            if (!$file->isUploaded()) {
                $fileName = $file->getNewName();
                if ($this->isRandomizeFileNameConfigured()) {
                    $fileName = $this->randomizeFileName(
                        $file->getNewName(),
                        $this->isPrependOriginalFileNameConfigured()
                    );
                    $file->renameName($fileName);
                }

                for ($i = 1; $this->isNotUniqueFilename($file); $i++) {
                    $fileName = $this->makeNewFilenameWithAppendix($file->getNewName(), $i);
                    $file->renameName($fileName);
                }

                $this->fileNames[] = $fileName;
            }
        }
    }

    /**
     * Create a new filename with an appendix up to _99 than randomize
     *
     *  image.png => image_01.png
     *  image_01.png => image_02.png
     */
    protected function makeNewFilenameWithAppendix(string $filename, int $iteration): string
    {
        if ($iteration >= 100) {
            return $this->randomizeFileName($filename);
        }

        $fileInfo = GeneralUtility::split_fileref($filename);
        $fileBody = $this->removeAppendingNumbersInString($fileInfo['filebody']);
        $appendix = '_' . sprintf('%02d', $iteration);
        return $fileBody . $appendix . '.' . $fileInfo['fileext'];
    }

    /**
     * @throws Exception
     */
    protected function isFileExistingInUploadFolder(File $file): bool
    {
        return file_exists($file->getNewPathAndFilename(true));
    }

    /**
     * @param false $prependOriginalFileName
     */
    protected function randomizeFileName(string $filename, bool $prependOriginalFileName = false): string
    {
        $fileInfo = pathinfo($filename);
        $randomizedFileName = '';
        if ($prependOriginalFileName) {
            $randomizedFileName .= $fileInfo['filename'] . '-';
        }

        $randomizedFileName .= StringUtility::getRandomString(32, false);
        return $randomizedFileName . ('.' . $fileInfo['extension']);
    }

    /**
     * @return File[]
     * @throws Exception
     */
    public function getFiles(): array
    {
        $this->eventDispatcher->dispatch(new UploadServiceGetFilesEvent($this));
        return $this->files;
    }

    /**
     * @param File[] $files
     */
    public function setFiles(array $files): UploadService
    {
        $this->files = $files;
        return $this;
    }

    public function addFile(File $file): void
    {
        $this->files[] = $file;
    }

    protected function isRandomizeFileNameConfigured(): bool
    {
        return $this->settings['misc']['file']['randomizeFileName'] === '1';
    }

    protected function isPrependOriginalFileNameConfigured(): bool
    {
        return $this->settings['misc']['file']['randomizePrependOriginalFileName'] === '1';
    }

    protected function getAllowedExtensions(): string
    {
        return $this->settings['misc']['file']['extension'];
    }

    protected function getArguments(): array
    {
        return FrontendUtility::getArguments(FrontendUtility::getPluginName());
    }

    /**
     * Remove appending numbers in filename strings
     *        image_01 => image
     *        image_01_02 => image_01
     */
    protected function removeAppendingNumbersInString(string $string): string
    {
        return preg_replace('~_\d+$~', '', $string);
    }

    /**
     * Check if this filename is not unique - could happen if
     * - This filename was used before
     * - Or if there is a file with this name in the target upload folder
     *
     * @throws Exception
     */
    protected function isNotUniqueFilename(File $file): bool
    {
        return in_array($file->getNewName(), $this->fileNames) || $this->isFileExistingInUploadFolder($file);
    }

    protected function isOptinConfirmWithExistingMail(array $arguments): bool
    {
        return !empty($arguments['hash']) && $arguments['action'] === 'optinConfirm' && $arguments['mail'] > 0;
    }
}
