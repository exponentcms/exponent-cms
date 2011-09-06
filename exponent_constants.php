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

/* exdoc
 * Filesystem Error Response: Success
 * @node Subsystems:Files
 */
define("SYS_FILES_SUCCESS",		0);

/* exdoc
 * Filesystem Error Response: Found File at Destination
 * @node Subsystems:Files
 */
define("SYS_FILES_FOUNDFILE",	1);

/* exdoc
 * Filesystem Error Response: Found Directory at Destination
 * @node Subsystems:Files
 */
define("SYS_FILES_FOUNDDIR",	2);

/* exdoc
 * Filesystem Error Response: Destination not writable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTWRITABLE",	3);

/* exdoc
 * Filesystem Error Response: Destination not readable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTREADABLE",	4);

/* exdoc
 * Filesystem Error Response: Destination not deletable
 * @node Subsystems:Files
 */
define("SYS_FILES_NOTDELETABLE",	5);

/* exdoc
 * The EQL header string for object dump file formats.
 * This header defines the version of EQL native to
 * the current implementation of the Backup Subsystem.
 * @node Subsystems:Backup
 */
define('EQL_HEADER','EQL-Exponent Query Language');

/** exdoc
 * UI Level of Preview - No management links of any kind should be shown.
 * @node Subsystems:Permissions
 */
define('UILEVEL_PREVIEW',0);

/** exdoc
* UI Level of Normal - Only normal management links (edit, delete, etc.) should be shown.
* @node Subsystems:Permissions
*/
define('UILEVEL_NORMAL',1);

/** exdoc
* UI Level of Permissions - Permission Management links (user and group perms) should be shown.
* @node Subsystems:Permissions
*/
define('UILEVEL_PERMISSIONS',2);

/** exdoc
* UI Level of Structure - All management links are shown.
* @node Subsystems:Permissions
*/
define('UILEVEL_STRUCTURE',3);

/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of module.
 * @node Subsystems:Core
 */
define("CORE_EXT_MODULE",1);

/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of theme.
 * @node Subsystems:Core
 */
define("CORE_EXT_THEME",2);

/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of subsystem.
 * @node Subsystems:Core
 */
define("CORE_EXT_SUBSYSTEM",3);

/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an 'extension type' to represent the
 * whole system
 * @node Subsystems:Core
 */
define("CORE_EXT_SYSTEM",4);

// Determines platform (OS), browser and version of the user
// Based on a phpBuilder article:
//   see http://www.phpbuilder.net/columns/tim20000821.php
if (!defined('EXPONENT_USER_OS')) {
    // 1. Platform
    if (strstr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
        define('EXPONENT_USER_OS', 'Win');
    } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
        define('EXPONENT_USER_OS', 'Mac');
    } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'Linux')) {
        define('EXPONENT_USER_OS', 'Linux');
    } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'Unix')) {
        define('EXPONENT_USER_OS', 'Unix');
    } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'OS/2')) {
        define('EXPONENT_USER_OS', 'OS/2');
    } else {
        define('EXPONENT_USER_OS', 'Other');
    }

    // 2. browser and version
    // (must check everything else before Mozilla)
	$log_version = array();
    if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[2]);
        define('EXPONENT_USER_BROWSER', 'OPERA');
    } else if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'IE');
    } else if (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'OMNIWEB');
    } else if (preg_match('@(Konqueror/)(.*)(;)@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[2]);
        define('EXPONENT_USER_BROWSER', 'KONQUEROR');
    } else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)
               && preg_match('@Safari/([0-9]*)@', $_SERVER['HTTP_USER_AGENT'], $log_version2)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1] . '.' . $log_version2[1]);
        define('EXPONENT_USER_BROWSER', 'SAFARI');
    } else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'MOZILLA');
    } else {
        define('EXPONENT_USER_BROWSER_VERSION', 0);
        define('EXPONENT_USER_BROWSER', 'OTHER');
    }
}

if (!defined('JS_FULL')) {
	/* exdoc
	 * The absolute path to Exponent's core javascript.
	 */
	define('JS_FULL',URL_FULL.'framework/core/js/');
}

// iconset base
if (!defined('ICON_RELATIVE')) {
	define('ICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/');
}
if (!defined('MIMEICON_RELATIVE')) {
	define('MIMEICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/mimetypes/');
}

if (!defined('YUI3_PATH')) {
    /*
	 *  YUI 3 Version Constant
	 *
	 * Changing the version here lets Exponent adjust where to look
	 */
	define('YUI3_VERSION', '3.3.0');
	define('YUI3_PATH', PATH_RELATIVE.'external/lissa/'.YUI3_VERSION.'/build/');
	define('YUI3_URL', URL_FULL.'external/lissa/'.YUI3_VERSION.'/build/');
}

if (!defined('YUI2_PATH')) {
    /*
	 *  YUI 2 Version Constant
	 *
	 * Changing the version here lets Exponent adjust where to look
	 */
	define('YUI2_VERSION', '2.9.0');
	define('YUI2_PATH', PATH_RELATIVE.'external/lissa/'.YUI2_VERSION.'/build/');
	define('YUI2_URL', URL_FULL.'external/lissa/'.YUI2_VERSION.'/build/');
}

?>