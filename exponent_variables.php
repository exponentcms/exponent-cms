<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

if (!defined('BASE')) {
	/*
	 * BASE Constant
	 *
	 * The BASE constant is the absolute path on the server filesystem, from the root (/ or C:\)
	 * to the Exponent directory.
	 */
	define('BASE',__realpath(dirname(__FILE__)).'/');
}
/*
 * EXPONENT Constant
 *
 * The EXPONENT constant defines the current Major.Minor version of Exponent/Exponent (i.e. 0.95).
 * It's definition also signals to other parts of the system that they are operating within the confines
 * of the Exponent Framework.  (Module actions check this -- if it is not defined, they must abort).
 */
define('EXPONENT', include(BASE.'exponent_version.php'));

if (!defined('PATH_RELATIVE')) {
	if (isset($_SERVER['DOCUMENT_ROOT'])) {
		/*
		 * PATH_RELATIVE Constant
		 *
		 * The PATH_RELATIVE constant is the web path to the Exponent directory,
		 * from the web root.  It is related to the BASE constant, but different.
		 */
		define('PATH_RELATIVE',str_replace(__realpath($_SERVER['DOCUMENT_ROOT']),'',BASE));
	} else {
		// FIXME: PATH_RELATIVE definition will break in certain parts when the server does not offer the Document_root.
		// FIXME: Notable, it breaks in the installer.
		// This triggers on IIS, which has no DOCUMENT_ROOT.
		define('PATH_RELATIVE',__realpath(dirname($_SERVER['SCRIPT_NAME']) . '/'));
	}
}

if (!defined('HOSTNAME')) {
	if (isset($_SERVER['HTTP_HOST'])) {
		define('HOSTNAME',$_SERVER['HTTP_HOST']);
	} else if (isset($_SERVER['SERVER_NAME'])) {
		define('HOSTNAME',$_SERVER['SERVER_NAME']);
	}
}

if (!defined('URL_BASE')) {
	/*
	 * URL_BASE Constant
	 *
	 * The URL_BASE constant is the base URL of the domain hosting the Exponent site.
	 * It does not include the PATH_RELATIVE information.  The automatic
	 * detection code can figure out if the server is running in SSL mode or not
	 */
	define('URL_BASE',((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . HOSTNAME);
}
if (!defined('URL_BASE_SECURE')) {
        /*
         * URL_BASE_SECURE Constant
         *
         * The URL_BASE constant is the base URL of the domain hosting the Exponent site.
         * It does not include the PATH_RELATIVE information.  The automatic
         * detection code can figure out if the server is running in SSL mode or not
         */
        define('URL_BASE_SECURE','https://'.HOSTNAME);
}
if (!defined('URL_FULL')) {
	/*
	 * URL_FULL Constant
	 *
	 * The URL_FULL constant is the full URL path to the Exponent directory.  The automatic
	 * detection code can figure out if the server is running in SSL mode or not.
	 */
	define('URL_FULL', URL_BASE.PATH_RELATIVE);
}

if (!defined('UPLOAD_DIRECTORY')) {
    /*
	 * UPLOAD_DIRECTORY Constant
	 *
	 * This is the directory where file uploads will go
	 */
	define('UPLOAD_DIRECTORY', BASE.'files/');
}

if (!defined('UPLOAD_DIRECTORY_RELATIVE')) {
    /*
	 * UPLOAD_DIRECTORY Constant
	 *
	 * This is the directory where file uploads will go
	 */
	define('UPLOAD_DIRECTORY_RELATIVE', 'files/');
}

if (defined('SCRIPT_EXP_RELATIVE')) {
	define('SCRIPT_RELATIVE', PATH_RELATIVE.SCRIPT_EXP_RELATIVE);
	define('SCRIPT_ABSOLUTE', BASE.SCRIPT_EXP_RELATIVE);
} else {
	ob_start();
	define('SCRIPT_RELATIVE', PATH_RELATIVE);
	define('SCRIPT_ABSOLUTE', BASE);
}

if (!defined('SCRIPT_FILENAME')) {
	define('SCRIPT_FILENAME', 'index.php');
}

include_once(BASE . '/subsystems/config/load.php');

?>