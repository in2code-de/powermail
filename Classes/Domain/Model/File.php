<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Events\GetNewPathAndFilenameEvent;
use In2code\Powermail\Utility\StringUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * File Model for single uploaded files
 */
class File
{
    /**
     * Field marker name
     *
     * @var string
     */
    protected string $marker = '';

    /**
     * Original name
     *
     * @var string
     */
    protected string $originalName = '';

    /**
     * Temporary uploaded name
     *
     * @var string|null
     */
    protected ?string $temporaryName = null;

    /**
     * New, cleaned and unique filename
     *
     * @var string
     */
    protected string $newName = '';

    /**
     * Is there a problem with this file?
     *
     * @var bool
     */
    protected bool $valid = true;

    /**
     * Like "image/png"
     *
     * @var string
     */
    protected string $type = '';

    /**
     * Filesize
     *
     * @var int
     */
    protected int $size = 0;

    /**
     * Uploadfolder for this file
     *
     * @var string
     */
    protected string $uploadFolder = 'uploads/tx_powermail/';

    /**
     * Already uploaded to uploadfolder?
     *
     * @var bool
     */
    protected bool $uploaded = false;

    /**
     * File must be renamed?
     *
     * @var bool
     */
    protected bool $renamed = false;

    /**
     * Related field
     *
     * @var Field|null
     */
    protected ?Field $field = null;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param string $marker
     * @param string $originalName
     * @param string $temporaryName
     */
    public function __construct(string $marker, string $originalName, string $temporaryName)
    {
        $this->marker = $marker;
        $this->originalName = $originalName;
        $this->temporaryName = $temporaryName;
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    /**
     * @return string
     */
    public function getMarker(): string
    {
        return $this->marker;
    }

    /**
     * @param string $marker
     * @return File
     */
    public function setMarker(string $marker): File
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     * @return File
     * @noinspection PhpUnused
     */
    public function setOriginalName(string $originalName): File
    {
        $this->originalName = $originalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryName(): string
    {
        return $this->temporaryName;
    }

    /**
     * @param string $temporaryName
     * @return File
     * @noinspection PhpUnused
     */
    public function setTemporaryName(string $temporaryName): File
    {
        $this->temporaryName = $temporaryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @param string $newName
     * @return File
     */
    public function setNewName(string $newName): File
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Set a new name and set renamed to true
     *
     * @param string $newName
     * @return File
     */
    public function renameName(string $newName): File
    {
        $this->newName = $newName;
        $this->setRenamed(true);
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     * @return File
     */
    public function setValid(bool $valid): File
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return File
     */
    public function setType(string $type): File
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return File
     */
    public function setSize(int $size): File
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadFolder(): string
    {
        return $this->uploadFolder;
    }

    /**
     * @param string $uploadFolder
     * @return File
     */
    public function setUploadFolder(string $uploadFolder): File
    {
        $this->uploadFolder = StringUtility::addTrailingSlash($uploadFolder);
        return $this;
    }

    /**
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    /**
     * @param bool $uploaded
     * @return File
     */
    public function setUploaded(bool $uploaded): File
    {
        $this->uploaded = $uploaded;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRenamed(): bool
    {
        return $this->renamed;
    }

    /**
     * @param bool $renamed
     * @return File
     */
    public function setRenamed(bool $renamed): File
    {
        $this->renamed = $renamed;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @param Field $field
     * @return File
     */
    public function setField(Field $field): File
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return bool
     */
    public function validFile(): bool
    {
        return $this->getSize() > 0 && $this->getOriginalName();
    }

    /**
     * Check if file is existing on the server
     *
     * @return bool
     */
    public function isFileExisting(): bool
    {
        return $this->isUploaded() && file_exists($this->getNewPathAndFilename(true));
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getNewPathAndFilename(bool $absolute = false): string
    {
        $pathAndFilename = $this->getUploadFolder() . $this->getNewName();
        if ($absolute === true) {
            $pathAndFilename = GeneralUtility::getFileAbsFileName($pathAndFilename);
        }
        /** @var GetNewPathAndFilenameEvent $event */
        $event = $this->eventDispatcher->dispatch(
            GeneralUtility::makeInstance(GetNewPathAndFilenameEvent::class, $pathAndFilename, $this)
        );
        return $event->getPathAndFilename();
    }
}
