<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use Symfony\Component\RateLimiter\LimiterInterface;

/**
 * Count the form submission against the rate limit.
 *
 * Only valid form submissions count against the rate limit.
 * This is implemented by checking for available tokens in the
 * RateLimitMethod spam shield, but consuming the tokens here.
 *
 * @link https://symfony.com/doc/7.3/rate_limiter.html
 */
class RateLimitFinisher extends AbstractFinisher
{
    /**
     * All the limiters that shall be consumed when the form is submitted.
     *
     * @var LimiterInterface[]
     */
    protected static array $limiters = [];

    /**
     * Marks the limiter as to be consumed when the mail is accepted and stored.
     */
    public static function markForConsumption(LimiterInterface $limiter): void
    {
        static::$limiters[] = $limiter;
    }

    /**
     * Consume a token for each rate limiter.
     */
    public function consumeLimitersFinisher(): void
    {
        foreach (static::$limiters as $limiter) {
            $limiter->consume(1);
        }
    }
}
