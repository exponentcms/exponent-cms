<?php
/*
 * This file is part of the Sift PHP framework.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Util;

final class Serializer
{
    /**
     * Serializes variables.
     *
     * @param array $vars
     *
     * @return string
     */
    public static function serialize($vars)
    {
        return serialize($vars);
    }

    /**
     * @param string $serialized
     *
     * @return array
     */
    public static function unserialize($serialized)
    {
        $unserialized = @unserialize($serialized);
        if ($unserialized === false) {
            throw new \RuntimeException('Error unserializing serialized data.');
        }

        return $unserialized;
    }
}
