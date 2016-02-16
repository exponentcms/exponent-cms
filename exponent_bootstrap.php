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
/** @define "BASE" "." */

/**
 * Minimum PHP version check
 */
//if (version_compare(PHP_VERSION, '5.3.1', 'lt')) {
//    echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>
//        PHP 5.3.1+ is required!  Please refer to the Exponent documentation for details:<br />
//        <a href=\"http://docs.exponentcms.org/docs/current/requirements-running-exponent-cms\" target=\"_blank\">http://docs.exponentcms.org/</a>
//        </h1>";
//    die();
//}

// Following code allows magic_quotes to be on without screwing stuff up.
// magic quotes feature was removed in php 5.4.0
if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
{
	/**
	 * @param $value
	 * @return mixed
	 */
	function stripslashes_deep($value) {
		return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	}

    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_POST = array_map('stripslashes_deep', $_POST);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

// for scripts that want to bootstrap minimally, we will need _realpath()
// if it isn't already defined.
if (!function_exists('__realpath')) {
	/**
	 * @param $path
	 * @return string
	 */
	function __realpath($path) {
		$path = str_replace('\\','/',realpath($path));
		if (!empty($path{1}) && $path{1} == ':') {
			// We can't just check for C:/, because windows users may have the IIS webroot on X: or F:, etc.
			$path = substr($path,2);
		}
		return $path;
	}
}

// Process user-defined constants first in overrides.php (if it exists)
@include_once('overrides.php');

// load constants for paths and other environment  not overridden in overrides.php
require_once(dirname(__realpath(__FILE__)) . '/exponent_constants.php');

// load the code version
require_once(BASE . 'exponent_version.php');

/*
 * EXPONENT Constant
 *
 * The EXPONENT Constant signals to other parts of the system that they are operating within the confines
 * of the Exponent v2 Framework.  (Module actions check this -- if it is not defined, they must abort).
 */
define('EXPONENT', EXPONENT_VERSION_MAJOR);

// load the constants from the global config and then default config settings
require_once(BASE . 'framework/core/subsystems/expSettings.php');  // we don't have our autoloader loaded yet

// Process PHP-wrapper settings (ini_sets and settings, and autoloader)
require_once(BASE . 'exponent_php_setup.php');

if (function_exists('gd_info')) {
	$info = gd_info();
	define('EXPONENT_HAS_GD',($info['GD Version'] == 'Not Supported' ? 0 : 1));
} else {
	define('EXPONENT_HAS_GD', 0);
}

?>