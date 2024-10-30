<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\UploadService;

final class UploadServicePreflightEvent
{
    public function __construct(protected UploadService $uploadService)
    {
    }

    public function getUploadService(): UploadService
    {
        return $this->uploadService;
    }
}
