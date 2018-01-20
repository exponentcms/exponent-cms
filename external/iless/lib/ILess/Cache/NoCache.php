<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Cache;

/**
 * Cache which does no storing at all.
 */
class NoCache extends Cache
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * @see CacheInterface::has
     */
    public function has($cacheKey)
    {
        return false;
    }

    /**
     * @see CacheInterface::get
     */
    public function get($cacheKey)
    {
    }

    /**
     * @see CacheInterface::set
     */
    public function set($cacheKey, $data, $ttl = null)
    {
    }

    /**
     * @see CacheInterface::remove
     */
    public function remove($cacheKey)
    {
    }

    /**
     * @see CacheInterface::clean
     */
    public function clean()
    {
    }
}
