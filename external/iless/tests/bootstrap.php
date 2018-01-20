<?php

/*
 * This file is part of the ILess
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit test bootstrap.
 */
require dirname(__FILE__) . '/../lib/ILess/Autoloader.php';
require dirname(__FILE__) . '/ILess/Test/TestCase.php';

define('ILESS_TEST_CACHE_DIR', sys_get_temp_dir() . '/iless-test');

if (is_dir(ILESS_TEST_CACHE_DIR)) {
    // clear the directory
    $files = glob(ILESS_TEST_CACHE_DIR . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir(ILESS_TEST_CACHE_DIR);
}

ILess\Autoloader::register();
