<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\File;

final class GetNewPathAndFilenameEvent
{
    /**
     * @var string
     */
    protected string $pathAndFilename;

    /**
     * @var File
     */
    protected File $file;

    /**
     * Constructor
     *
     * @param string $pathAndFilename
     * @param File $file
     */
    public function __construct(string $pathAndFilename, File $file)
    {
        $this->pathAndFilename = $pathAndFilename;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getPathAndFilename(): string
    {
        return $this->pathAndFilename;
    }

    /**
     * @param string $pathAndFilename
     * @return GetNewPathAndFilenameEvent
     */
    public function setPathAndFilename(string $pathAndFilename): GetNewPathAndFilenameEvent
    {
        $this->pathAndFilename = $pathAndFilename;
        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}
