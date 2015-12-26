#!/usr/bin/env php
<?php
    ##################################################
    #
    # Copyright (c) 2004-2016 OIC Group, Inc.
    #
    # This file is part of Exponent
    #
    # Exponent is free software; you can redistribute
    # it and/or modify it under the terms of the GNU
    # General Public License as published by the Free
    # Software Foundation; either version 2 of the
    # License, or (at your option) any later version.
    #
    # GPL: http://www.gnu.org/licenses/gpl.txt
    #
    ##################################################

    /**
    * This is meant to be called from cron to import products from a text tile.
    */

    include_once('../exponent.php');

    $code = 'abc123';
    $code_in = '';
    $filename = new stdClass();

    if (php_sapi_name() == 'cli') {
        $nl = "\n";
        if (!empty($_SERVER['argc'])) for ($ac = 1; $ac < $_SERVER['argc']; $ac++) {
            if ($_SERVER['argv'][$ac] == '-c') {
                $ac++;
                $code_in = $_SERVER['argv'][$ac];
            } elseif (!empty($_SERVER['argv'][$ac])) {
                $filename->path = BASE . $_SERVER['argv'][$ac];
            }
        }
    } else {
        $nl = '<br>';
        if (!empty($_GET['code'])) {
            $code_in = $_GET['code'];
        }
        if (!empty($_GET['filename'])) {
            $filename->path = BASE . $_GET['filename'];
        }
    }
    if ($code_in !== $code || empty($filename->path) || !file_exists($filename->path)) {
        print $nl . gt('Import Products Script Error') . $nl;
        exit(); //NOTE security code MUST be correct and file must exist
    }

    $store = new storeController();
    $store->importProduct($filename);

?>