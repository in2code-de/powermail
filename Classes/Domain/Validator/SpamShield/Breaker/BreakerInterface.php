<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

/**
 * Interface BreakerInterface
 */
interface BreakerInterface
{
    public function isDisabled(): bool;
}
