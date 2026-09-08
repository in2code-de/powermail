<?php
declare(strict_types = 1);
namespace In2code\Powermail\Fluid\Cache;

use TYPO3Fluid\Fluid\Core\Cache\FluidCacheInterface;
use TYPO3Fluid\Fluid\Core\Cache\FluidCacheWarmerInterface;
use TYPO3Fluid\Fluid\Core\Cache\StandardCacheWarmer;

/**
 * Class NullFluidCache
 *
 * Keeps compiled templates of parsed strings out of the persistent fluid_template cache.
 *
 * Without this, every distinct value powermail parses - and a sender name or subject can be supplied
 * by a website visitor - is compiled into its own PHP class file below the fluid_template cache,
 * which is unbounded growth driven from the outside. These strings are short and cheap to parse, so
 * there is nothing to gain from caching them.
 */
class NullFluidCache implements FluidCacheInterface
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
    public function set($name, $value)
    {
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function flush($name = null)
    {
    }

    /**
     * Nothing is ever written to this cache, so there is nothing to warm up either - the default
     * warmer is returned to satisfy the interface.
     *
     * @return FluidCacheWarmerInterface
     */
    public function getCacheWarmer()
    {
        return new StandardCacheWarmer();
    }
}
