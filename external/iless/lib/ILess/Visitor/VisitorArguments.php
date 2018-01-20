<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Visitor;

/**
 * Visitor arguments.
 */
final class VisitorArguments
{
    /**
     * Visit deeper flag.
     *
     * @var bool
     */
    public $visitDeeper = true;

    /**
     * Constructor.
     *
     * @param array $arguments
     */
    public function __construct($arguments = [])
    {
        foreach ($arguments as $argument => $value) {
            $this->$argument = $value;
        }
    }
}
