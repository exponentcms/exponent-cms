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

    $param_array = array();
    $verbose = false;
    $recur = true;
    $total_new = 0;

    include_once('../exponent.php');
    if (php_sapi_name() == 'cli') {
        $nl = "\n";
        if (!empty($_SERVER['argc'])) for ($ac = 1; $ac < $_SERVER['argc']; $ac++) {
            if ($_SERVER['argv'][$ac] == '-v') {
                $verbose = true;
            } elseif (!empty($_SERVER['argv'][$ac])) {
                $version_title = $_SERVER['argv'][$ac];
                $version = $db->selectValue('help_version', 'id', 'version="' . $_SERVER['argv'][$ac] . '"');
            }
        }
    } else {
        $nl = '<br>';
        if (!empty($_GET['verbose'])) {
            $verbose = true;
        }
        if (!empty($_GET['version'])) {
            $version_title = $_GET['version'];
            $version = $db->selectValue('help_version', 'id', 'version="' . expString::sanitize($_GET['version']) . '"');
        }
    }
    /**
     * find_help.php - attempts to auto-check all ExponentCMS help links
     * by collecting them and checking them against the doc.exponentcms.org db tables
     */
    print $nl . "Checking the Exponent Help System!" . $nl . $nl;
    print "Grabbing links from the folders!" . $nl;
    parse_files('..', false);
    $filelist = array('../cron', '../framework', '../install', '../themes');
    foreach ($filelist as $file) {
        parse_files($file);
    }

    print $nl . "Completed grabbing " . $total_new . " Total Help Links!" . $nl . $nl;
    if (empty($version)) {
        $version = $db->selectValue('help_version', 'id', 'is_current=1');
        $version_title = 'Current';
    }
    print "Using Help Version - " . $version_title . "!" . $nl . $nl;

    // match condensed lists against db tables
    print "List of Missing Help Page Links" . $nl;
    $pages = array_unique($param_array['page']);
    sort($pages);
    foreach ($pages as $page) {
        if (!$db->selectObject('section', 'sef_name=' . $page)) {
            print $page . $nl;
        }
    }

    print $nl . "List of Missing Help Document Links" . $nl;
    $docs = array_unique(array_merge($param_array['doc'],$param_array['module']));
    sort($docs);
    foreach ($docs as $doc) {
        if (!$db->selectObject('help', 'sef_url=' . $doc . ' AND help_version_id=' . $version)) {
            print $doc . $nl;
        }
    }

    print $nl . "Completed Checking the Exponent Help System!" . $nl;

    // traverse all the files
    function parse_files($filename, $recurse = true) {
        global $recur, $verbose, $nl;

        if ($verbose) print "Grabbing help links" . $nl;
        $recur = $recurse;
        if (is_dir($filename)) { // go through directory
            do_dir($filename);
        } else { // do file
            $pi = pathinfo($filename);
            if (empty($pi['extension'])) $pi['extension'] = null;
            do_file($filename, $pi['extension']);
        }
    }

    // parse all the files
    // processes file for assoc strings
    function do_file($file, $fileext) {
        if ($fileext == 'tpl') {
            do_extract($file);
        }
    }

    // go through a directory
    function do_dir($dir) {
        global $recur;

        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $entry = $dir . '/' . $entry;

            if (is_dir($entry)) { // if a directory, go through it
                if ($recur) do_dir($entry);
            } else { // if file, parse only if extension is matched
                $pi = pathinfo($entry);
                if (empty($pi['extension'])) $pi['extension'] = null;
                if (isset($pi['extension']) && $pi['extension'] == 'tpl') {
                    do_file($entry, $pi['extension']);
                }
            }
        }

        $d->close();
    }

    //parse the help function lines
    // rips gettext strings from $file and prints them in C format
    function do_extract($file) {
        global $total_new, $param_array, $verbose, $nl;

    // regex for the help shortcut function
        $regex_help = '/(?<=help\s)((page=[\'"]|[^\'"])*)([^}]*)(?=\})/';
    // regex for the parameter list
        $regex_params = '/([^=\s]+)=["\']([^"\']+)["\']/';
        $content = @file_get_contents($file);
        if (empty($content)) {
            return;
        }
        preg_match_all(
            $regex_help,
            $content,
            $matches,
            PREG_PATTERN_ORDER
        );

        if ($verbose) print "$file" . " - ";
        $num_added = 0;
        $parsed = null;

        for ($i = 0, $iMax = count($matches[0]); $i < $iMax; $i++) {
    //        str_replace('"', "\'", $matches[0][$i]); // remove the killer double-quotes

            // segregate params
            preg_match_all(
                $regex_params,
                $matches[0][$i],
                $parsed,
                PREG_PATTERN_ORDER
            );
            //print_r(explode('=',$s[0][0]));
            foreach ($parsed[0] as $pair) {
                if (strpos($pair, '=')) {
                    list($key, $val) = explode('=', $pair, 2);
                    $param_array[trim($key)][] = trim($val);
                    $num_added++;
                }
            }
        }

        $total_new += $num_added;
        if ($verbose) print $num_added . $nl;
    }

    function output($text) {
        global $nl;

        if (!is_array($text)) $text = array($text);
        foreach ($text as $string) {
            print $string . $nl;
        }
    }

?>