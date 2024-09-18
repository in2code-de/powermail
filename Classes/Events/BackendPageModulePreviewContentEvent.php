<?php

declare(strict_types=1);

namespace In2code\Powermail\Events;

use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;

final class BackendPageModulePreviewContentEvent
{
    public function __construct(
        private string $preview,
        private readonly GridColumnItem $item
    ) {
    }

    public function getPreview(): string
    {
        return $this->preview;
    }

    public function setPreview(string $preview): void
    {
        $this->preview = $preview;
    }

    public function getItem(): GridColumnItem
    {
        return $this->item;
    }
}
