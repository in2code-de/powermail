<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\UploadService;

final class UploadServicePreflightEvent
{
    /**
     * @var UploadService
     */
    protected UploadService $uploadService;

    /**
     * @param UploadService $uploadService
     */
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * @return UploadService
     */
    public function getUploadService(): UploadService
    {
        return $this->uploadService;
    }
}
