<?php

/*
 * This file is part of the Sift PHP framework.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ILess\Output;

use ILess\FileInfo;

/**
 * Output interface.
 */
interface OutputInterface
{
    /**
     * Adds a chunk to the stack.
     *
     * @param string $chunk The chunk to output
     * @param FileInfo $fileInfo The file information
     * @param int $index The index
     * @param mixed $mapLines
     *
     * @return StandardOutput
     */
    public function add($chunk, FileInfo $fileInfo = null, $index = 0, $mapLines = null);

    /**
     * Is the output empty?
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Converts the output to string.
     *
     * @return string
     */
    public function toString();
}
