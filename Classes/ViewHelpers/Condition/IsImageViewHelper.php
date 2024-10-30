<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsImageViewHelper
 */
class IsImageViewHelper extends AbstractViewHelper
{
    /**
     * Webimage Formats
     *
     * @var array
     */
    protected $imageExtensions = [
        'jpg',
        'jpeg',
        'bmp',
        'gif',
        'png',
    ];

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'Path', true);
    }

    /**
     * Check if Path or File is an image
     */
    public function render(): bool
    {
        $fileInfo = pathinfo((string) $this->arguments['path']);
        return in_array($fileInfo['extension'], $this->imageExtensions);
    }
}
