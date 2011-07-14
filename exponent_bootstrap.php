<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

# Following code taken from http://us4.php.net/manual/en/function.get-magic-quotes-gpc.php
#   - it allows magic_quotes to be on without screwing stuff up.
# magic quotes were removed in php6
if(phpversion() < 6) { 
	if (get_magic_quotes_gpc()) {
		function stripslashes_deep($value) {
			return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		}

		$_POST = stripslashes_deep($_POST);
		$_GET = stripslashes_deep($_GET);
		$_COOKIE = stripslashes_deep($_COOKIE);
	}
}

// exponent.php (the file that includes this file the most) will define this for its own purposes
// but for other scripts that want to bootstrap minimally, we will need it, so only define it
// if it isn't already defined.
if (!function_exists('__realpath')) {
	function __realpath($path) {
		$path = str_replace('\\','/',realpath($path));
		if ($path{1} == ':') {
			// We can't just check for C:/, because windows users may have the IIS webroot on X: or F:, etc.
			$path = substr($path,2);
		}
		return $path;
	}
}

// Process user-defined constants in overrides.php
// THIS CANNOT USE __realpath like the others, since this file could be
// symlinked through the multi-site manager
include_once('overrides.php');

// Auto-detect whatever variables the user hasn't overridden in overrides.php
include_once(dirname(__realpath(__FILE__)) . '/exponent_variables.php');

// Set the default timezone.
if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set(DISPLAY_DEFAULT_TIMEZONE);
}

// Process PHP-wrapper settings (ini_sets and setting detectors)
include_once(dirname(__realpath(__FILE__)) . '/exponent_setup.php');

// Initialize the PHP4 Compatibility Layer
include(BASE.'compat.php');

?>