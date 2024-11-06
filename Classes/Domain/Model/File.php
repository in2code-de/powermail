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
     * New, cleaned and unique filename
     */
    protected string $newName = '';

    /**
     * Is there a problem with this file?
     */
    protected bool $valid = true;

    /**
     * Like "image/png"
     */
    protected string $type = '';

    /**
     * Filesize
     */
    protected int $size = 0;

    /**
     * Uploadfolder for this file
     */
    protected string $uploadFolder = 'uploads/tx_powermail/';

    /**
     * Already uploaded to uploadfolder?
     */
    protected bool $uploaded = false;

    /**
     * File must be renamed?
     */
    protected bool $renamed = false;

    /**
     * Related field
     */
    protected ?Field $field = null;

    private readonly EventDispatcherInterface $eventDispatcher;

    /**
     * @param string $temporaryName
     */
    public function __construct(/**
     * Field marker name
     */
        protected string $marker, /**
     * Original name
     */
        protected string $originalName, /**
     * Temporary uploaded name
     */
        protected ?string $temporaryName
    ) {
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
    }

    public function getMarker(): string
    {
        return $this->marker;
    }

    public function setMarker(string $marker): File
    {
        $this->marker = $marker;
        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @noinspection PhpUnused
     */
    public function setOriginalName(string $originalName): File
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getTemporaryName(): string
    {
        return $this->temporaryName;
    }

    /**
     * @noinspection PhpUnused
     */
    public function setTemporaryName(string $temporaryName): File
    {
        $this->temporaryName = $temporaryName;
        return $this;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }

    public function setNewName(string $newName): File
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Set a new name and set renamed to true
     */
    public function renameName(string $newName): File
    {
        $this->newName = $newName;
        $this->setRenamed(true);
        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): File
    {
        $this->valid = $valid;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): File
    {
        $this->type = $type;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): File
    {
        $this->size = $size;
        return $this;
    }

    public function getUploadFolder(): string
    {
        return $this->uploadFolder;
    }

    public function setUploadFolder(string $uploadFolder): File
    {
        $this->uploadFolder = StringUtility::addTrailingSlash($uploadFolder);
        return $this;
    }

    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    public function setUploaded(bool $uploaded): File
    {
        $this->uploaded = $uploaded;
        return $this;
    }

    public function isRenamed(): bool
    {
        return $this->renamed;
    }

    public function setRenamed(bool $renamed): File
    {
        $this->renamed = $renamed;
        return $this;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function setField(Field $field): File
    {
        $this->field = $field;
        return $this;
    }

    public function validFile(): bool
    {
        return $this->getSize() > 0 && $this->getOriginalName();
    }

    /**
     * Check if file is existing on the server
     */
    public function isFileExisting(): bool
    {
        return $this->isUploaded() && file_exists($this->getNewPathAndFilename(true));
    }

    public function getNewPathAndFilename(bool $absolute = false): string
    {
        $pathAndFilename = $this->getUploadFolder() . $this->getNewName();
        if ($absolute) {
            $pathAndFilename = GeneralUtility::getFileAbsFileName($pathAndFilename);
        }

        /** @var GetNewPathAndFilenameEvent $event */
        $event = $this->eventDispatcher->dispatch(
            GeneralUtility::makeInstance(GetNewPathAndFilenameEvent::class, $pathAndFilename, $this)
        );
        return $event->getPathAndFilename();
    }
}
