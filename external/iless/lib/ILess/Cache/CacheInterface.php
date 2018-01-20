<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Cache;

/**
 * Cache interface.
 */
interface CacheInterface
{
    /**
     * Returns the cached item for given $cacheKey.
     *
     * @param string $cacheKey
     *
     * @return mixed
     */
    public function get($cacheKey);

    /**
     * Sets the cache for the $cacheKey.
     *
     * @param string $cacheKey The cache key
     * @param mixed $data The data to store
     * @param int $ttl The time to live
     *
     * @return bool
     */
    public function set($cacheKey, $data, $ttl = null);

    /**
     * Is the cacheKey stored?
     *
     * @param string $cacheKey
     */
    public function has($cacheKey);

    /**
     * Removes item from cache.
     *
     * @param string $cacheKey
     */
    public function remove($cacheKey);

    /**
     * Removes everything from the cache.
     */
    public function clean();
}
