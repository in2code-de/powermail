<?php

declare(strict_types=1);

namespace In2code\Powermail\Storage;

use Symfony\Component\RateLimiter\LimiterStateInterface;
use Symfony\Component\RateLimiter\Policy\SlidingWindow;
use Symfony\Component\RateLimiter\Policy\TokenBucket;
use Symfony\Component\RateLimiter\Policy\Window;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * Copy of TYPO3's internal CachingFrameworkStorage.
 *
 * \TYPO3\CMS\Core\RateLimiter\Storage\CachingFrameworkStorage
 */
class RateLimitStorage implements StorageInterface
{
    private FrontendInterface $cacheInstance;

    public function __construct(CacheManager $cacheInstance)
    {
        $this->cacheInstance = $cacheInstance->getCache('ratelimiter');
        $this->cacheInstance->collectGarbage();
    }

    public function save(LimiterStateInterface $limiterState): void
    {
        $this->cacheInstance->set(
            sha1($limiterState->getId()),
            serialize($limiterState),
            [],
            $limiterState->getExpirationTime()
        );
    }

    public function fetch(string $limiterStateId): ?LimiterStateInterface
    {
        $cacheItem = $this->cacheInstance->get(sha1($limiterStateId));
        if ($cacheItem) {
            $value = unserialize($cacheItem, ['allowed_classes' => [Window::class, SlidingWindow::class, TokenBucket::class]]);
            if ($value instanceof LimiterStateInterface) {
                return $value;
            }
        }

        return null;
    }

    public function delete(string $limiterStateId): void
    {
        $this->cacheInstance->remove(sha1($limiterStateId));
    }
}
