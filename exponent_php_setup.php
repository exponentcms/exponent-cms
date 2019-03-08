<?php

##################################################
#
# Copyright (c) 2004-2019 OIC Group, Inc.
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

// Set up sessions to use cookies, NO MATTER WHAT
ini_set('session.use_cookies',1);
// Set the save_handler to files
ini_set('session.save_handler','files');
// Set the content compression to zlib
//ini_set("zlib.output_compression", "4096");
//ini_set("allow_url_fopen",1);
//ini_set('session.cache_limiter','public');
//session_cache_limiter(false);

if (DEVELOPMENT) {
	// In development mode, we need to turn on full throttle error reporting.
	// Display all errors (some production servers have this set to off)
	ini_set('display_errors',1);
	// Up the ante on the error reporting so we can see notices as well.
	ini_set('error_reporting',E_ALL);
//    ini_set('error_reporting',E_ALL & ~E_STRICT);  // hide 'strict' warning in v5.4
	// This is rarely set to true, but the first time it is, we'll be ready.
	ini_set('ignore_repeated_errors',0);
} else {
	// We can't be showing errors in a production environment...
	ini_set('display_errors',0);
	ini_set('ignore_repeated_errors',1);
}

// Set the default timezone.
@date_default_timezone_set(DISPLAY_DEFAULT_TIMEZONE);

// Initialize the AutoLoader subsystem - for objects we want loaded on the fly
$auto_dirs = array(
    BASE.'framework/core/subsystems',
    BASE.'framework/core/controllers',
   	BASE.'framework/core/models',
	BASE.'framework/core/forms',
	BASE.'framework/core/forms/controls',
	BASE.'framework/modules/ecommerce/billingcalculators',
	BASE.'framework/modules/ecommerce/shippingcalculators',
//	BASE.'framework/modules/ecommerce/products/controllers',  //FIXME does NOT exist
	BASE.'framework/modules/ecommerce/products/models',  // models
);
/**
 * Stores the search order locations for models & controllers
 * @var array $auto_dirs2
 * @name $auto_dirs2
 */
$auto_dirs2 = array(
	BASE.'framework/modules'
);

/** exdoc
 * This function overrides the default PHP5 autoloader,
 * and instead looks at the $auto_dirs global to look
 * for class files.  This function is automatically
 * invoked in PHP5
 *
 * @param string $class The name of the class to look for.
 * @node Autoloader
 */
function expLoadClasses($class) {
	global $auto_dirs, $auto_dirs2;
	foreach ($auto_dirs as $auto_dir) {
		if (is_readable($auto_dir.'/'.$class.'.php')) {
			include_once($auto_dir.'/'.$class.'.php');
			return;
		}
	}

	// recursive function used for (auto?)loading 2.0 modules controllers & models
	foreach ($auto_dirs2 as $dir) {
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir.'/'.$file) && ($file != '..' && $file != '.')) {
					// look at controllers
					$dirpath = $dir.'/'.$file.'/controllers';
					if (file_exists($dirpath)) {
						$controller_dir = opendir($dirpath);
						while (($ctl_file = readdir($controller_dir)) !== false) {
							if (substr($ctl_file,0,-4) == $class && substr($ctl_file,-4,4) == ".php") {
								include_once($dirpath.'/'.$ctl_file);
								return;
							}
						}
					}
					// look at models
					$dirpath = $dir.'/'.$file.'/models';
					if (file_exists($dirpath)) {
						$controller_dir = opendir($dirpath);
						while (($ctl_file = readdir($controller_dir)) !== false) {
							if (substr($ctl_file,0,-4) == $class && substr($ctl_file,-4,4) == ".php") {
								include_once($dirpath.'/'.$ctl_file);
								return;
							}
						}
					}
				}
			}
		}
	}
}

spl_autoload_register('expLoadClasses');

?>