<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Node;

/**
 * Makeable important interface.
 */
interface MarkableAsReferencedInterface
{
    /**
     * Marks as referenced.
     */
    public function markReferenced();
}
