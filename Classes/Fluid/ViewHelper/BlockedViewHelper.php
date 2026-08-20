<?php

declare(strict_types=1);
namespace In2code\Powermail\Fluid\ViewHelper;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Placeholder that RestrictedViewHelperResolver returns instead of a ViewHelper which is not on the
 * allowlist. It renders nothing and swallows every argument, so a blocked ViewHelper neither
 * executes nor breaks the parsing of the rest of the string.
 *
 * This class deliberately lives outside Classes/ViewHelpers/ so that it cannot be addressed from a
 * template.
 */
final class BlockedViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     */
    public function render(): string
    {
        return '';
    }

    /**
     * Accept anything - a blocked ViewHelper must not raise "undeclared argument" errors for the
     * arguments the template happens to pass.
     *
     * @param array $arguments
     * @return void
     */
    public function validateAdditionalArguments(array $arguments): void
    {
    }
}
