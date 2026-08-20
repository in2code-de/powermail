<?php

declare(strict_types=1);
namespace In2code\Powermail\Fluid\Cache;

use TYPO3Fluid\Fluid\Core\Cache\FluidCacheInterface;
use TYPO3Fluid\Fluid\Core\Cache\FluidCacheWarmerInterface;
use TYPO3Fluid\Fluid\Core\Cache\StandardCacheWarmer;

/**
 * Keeps compiled templates of parsed strings out of the persistent fluid_template cache.
 *
 * Without this, every distinct value powermail parses - and a sender name or subject can be supplied
 * by a website visitor - is compiled into its own PHP class file below typo3temp/var/cache/, which is
 * unbounded growth driven from the outside. These strings are short and cheap to parse, so there is
 * nothing to gain from caching them.
 */
final class NullFluidCache implements FluidCacheInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value): void
    {
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function flush($name = null): void
    {
    }

    /**
     * @return FluidCacheWarmerInterface
     */
    public function getCacheWarmer(): FluidCacheWarmerInterface
    {
        return new StandardCacheWarmer();
    }
}
