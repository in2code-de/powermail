<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\File;

final class GetNewPathAndFilenameEvent
{
    /**
     * Constructor
     */
    public function __construct(protected string $pathAndFilename, protected File $file)
    {
    }

    public function getPathAndFilename(): string
    {
        return $this->pathAndFilename;
    }

    public function setPathAndFilename(string $pathAndFilename): GetNewPathAndFilenameEvent
    {
        $this->pathAndFilename = $pathAndFilename;
        return $this;
    }

    public function getFile(): File
    {
        return $this->file;
    }
}
