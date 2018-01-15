<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

/**
 * Interface BreakerInterface
 */
interface BreakerInterface
{

    /**
     * @return bool
     */
    public function isDisabled(): bool;
}
