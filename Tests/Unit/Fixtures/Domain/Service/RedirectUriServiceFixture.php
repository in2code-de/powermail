<?php

namespace In2code\Powermail\Tests\Unit\Fixtures\Domain\Service;

use In2code\Powermail\Domain\Service\RedirectUriService;

/**
 * Class RedirectUriServiceFixture
 */
class RedirectUriServiceFixture extends RedirectUriService
{
    /**
     * @var array
     */
    protected $typoScriptFixture = [];

    /**
     * @var array
     */
    protected $flexFormFixture = [];

    /**
     * Get typoscript
     *
     * @return array
     */
    protected function getOverwriteTypoScript(): ?array
    {
        return $this->typoScriptFixture;
    }

    protected function getFlexFormArray(): ?array
    {
        return $this->flexFormFixture;
    }
}
